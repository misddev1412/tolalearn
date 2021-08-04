<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CommentsController extends Controller
{
    public $page = '';
    public $item = '';
    public $item_column = '';

    public function __construct(Request $request)
    {
        $page = Route::current()->parameter('page');
        $this->page = $page;

        if ($page == 'webinars') {
            $this->item = 'webinar';
            $this->item_column = 'webinar_id';
        } else {
            $this->item = 'blog';
            $this->item_column = 'blog_id';
        }
    }

    public function index(Request $request)
    {
        $this->authorize('admin_' . $this->item . '_comments');

        $query = Comment::whereNotNull($this->item_column);

        $totalComments = deepClone($query)->count();
        $publishedComments = deepClone($query)->where('status', 'active')->count();
        $pendingComments = deepClone($query)->where('status', 'pending')->count();
        $commentReports = CommentReport::whereNotNull($this->item_column)->count();

        $query = $this->filters($query, $request);
        $comments = $query->with([
            $this->item, // webinar or blog relation
            'user' => function ($query) {
                $query->select('id', 'full_name');
            }
        ])->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'itemRelation' => $this->item,
            'page' => $this->page,
            'pageTitle' => trans('admin/pages/comments.'.($this->page == 'webinars' ? 'classes_comments' : 'blog_comments')),
            'comments' => $comments,
            'totalComments' => $totalComments,
            'publishedComments' => $publishedComments,
            'pendingComments' => $pendingComments,
            'commentReports' => $commentReports,
        ];

        $user_ids = $request->get('user_ids');
        $webinar_ids = $request->get('webinar_ids');
        $post_ids = $request->get('post_ids');

        if (!empty($user_ids)) {
            $data['users'] = User::select('id', 'full_name')->whereIn('id', $user_ids)->get();
        }

        if (!empty($webinar_ids)) {
            $data['webinars'] = Webinar::select('id', 'title')->whereIn('id', $webinar_ids)->get();
        }

        if (!empty($post_ids)) {
            $data['blog'] = Blog::select('id', 'title')->whereIn('id', $post_ids)->get();
        }

        return view('admin.comments.comments', $data);
    }

    private function filters($query, $request)
    {
        $title = $request->get('title', null);
        $date = $request->get('date', null);
        $webinar_ids = $request->get('webinar_ids', null);
        $user_ids = $request->get('user_ids', null);
        $post_ids = $request->get('post_ids', null);
        $status = $request->get('status', null);

        if (!empty($title)) {
            $query->where('title', 'like', "%$title%");
        }

        if (!empty($date)) {
            $timestamp = strtotime($date);
            $beginOfDay = strtotime("today", $timestamp);
            $endOfDay = strtotime("tomorrow", $beginOfDay) - 1;

            $query->whereBetween('created_at', [$beginOfDay, $endOfDay]);
        }

        if (!empty($webinar_ids)) {
            $query->whereIn('webinar_id', $webinar_ids);
        }

        if (!empty($user_ids)) {
            $query->whereIn('user_id', $user_ids);
        }

        if (!empty($post_ids)) {
            $query->whereIn('id', $post_ids);
        }

        if (!empty($status) and in_array($status, ['active', 'pending'])) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function toggleStatus($page, $comment_id)
    {
        $this->authorize('admin_' . $this->item . '_comments_status');

        $comment = Comment::where('id', $comment_id)
            ->whereNotNull($this->item_column)
            ->first();


        if (!empty($comment)) {
            $comment->update([
                'status' => ($comment->status == 'pending') ? 'active' : 'pending',
            ]);

            if ($comment->status == 'active' and !empty($comment->webinar_id)) {
                $webinar = Webinar::FindOrFail($comment->webinar_id);
                $commentedUser = User::findOrFail($comment->user_id);

                $notifyOptions = [
                    '[c.title]' => $webinar->title,
                    '[u.name]' => $commentedUser->full_name
                ];
                sendNotification('new_comment', $notifyOptions, $webinar->teacher_id);
            }
        }

        return redirect()->back();
    }

    public function edit($page, $comment_id)
    {
        $this->authorize('admin_' . $this->item . '_comments_edit');

        $comment = Comment::where('id', $comment_id)
            ->whereNotNull($this->item_column)
            ->first();

        if (!empty($comment)) {
            $data = [
                'pageTitle' => trans('admin/pages/comments.edit_comment'),
                'itemRelation' => $this->item,
                'page' => $this->page,
                'comment' => $comment,
            ];

            return view('admin.comments.comment_edit', $data);
        }

        abort(404);
    }

    public function update(Request $request, $page, $comment_id)
    {
        $this->authorize('admin_' . $this->item . '_comments_edit');

        $this->validate($request, [
            'comment' => 'required|string'
        ]);

        $comment = Comment::where('id', $comment_id)
            ->whereNotNull($this->item_column)
            ->first();

        if (!empty($comment)) {
            $comment->update([
                'comment' => $request->get('comment'),
            ]);
        }

        return redirect()->back();
    }

    public function reply($page, $comment_id)
    {
        $this->authorize('admin_' . $this->item . '_comments_reply');

        $comment = Comment::where('id', $comment_id)
            ->whereNotNull($this->item_column)
            ->with('replies')
            ->first();

        if (!empty($comment)) {
            $data = [
                'pageTitle' => trans('admin/pages/comments.reply_comment'),
                'itemRelation' => $this->item,
                'page' => $this->page,
                'comment' => $comment,
            ];

            return view('admin.comments.comment_reply', $data);
        }

        abort(404);
    }

    public function storeReply(Request $request, $page, $comment_id)
    {
        $this->authorize('admin_' . $this->item . '_comments_reply');

        $this->validate($request, [
            'comment' => 'required|string'
        ]);

        $comment = Comment::where('id', $comment_id)
            ->whereNotNull($this->item_column)
            ->first();

        if (!empty($comment)) {

            Comment::create([
                'user_id' => auth()->user()->id,
                'comment' => $request->get('comment'),
                $this->item_column => $comment->{$this->item_column},
                'reply_id' => $comment->id,
                'status' => 'active',
                'created_at' => time()
            ]);
        }

        return redirect()->back();
    }

    public function delete(Request $request, $page, $comment_id)
    {
        $this->authorize('admin_' . $this->item . '_comments_delete');

        $comment = Comment::where('id', $comment_id)
            ->whereNotNull($this->item_column)
            ->first();

        if (!empty($comment)) {
            $comment->delete();
        }

        if (!empty($request->get('redirect_to'))) {
            return redirect($request->get('redirect_to'));
        }

        return redirect()->back();
    }

    public function reports()
    {
        $this->authorize('admin_' . $this->item . '_comments_reports');

        $reports = CommentReport::whereNotNull($this->item_column)
            ->with([$this->item, 'user' => function ($query) {
                $query->select('id', 'full_name');
            }])->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/comments.comments_reports'),
            'itemRelation' => $this->item,
            'page' => $this->page,
            'reports' => $reports,
        ];

        return view('admin.comments.reports', $data);
    }

    public function reportShow($page, $id)
    {
        $this->authorize('admin_' . $this->item . '_comments_reports');

        $report = CommentReport::where('id', $id)
            ->whereNotNull($this->item_column)
            ->with(['comment', 'user' => function ($query) {
                $query->select('id', 'full_name');
            }])->first();

        if (!empty($report)) {
            $data = [
                'pageTitle' => trans('admin/pages/comments.comments_reports'),
                'itemRelation' => $this->item,
                'page' => $this->page,
                'report' => $report,
                'comment' => $report->comment
            ];

            return view('admin.comments.report_show', $data);
        }

        abort(404);
    }

    public function reportDelete(Request $request, $page, $id)
    {
        $this->authorize('admin_' . $this->item . '_comments_reports');

        $report = CommentReport::where('id', $id)
            ->whereNotNull($this->item_column)
            ->first();

        if (!empty($report)) {
            $report->delete();
        }

        if (!empty($request->get('redirect_to'))) {
            return redirect($request->get('redirect_to'));
        }

        return redirect()->back();
    }
}
