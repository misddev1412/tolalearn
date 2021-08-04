<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Webinar;
use App\Models\WebinarReview;
use Illuminate\Http\Request;

class WebinarReviewController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request, [
            'webinar_id' => 'required',
            'content_quality' => 'required',
            'instructor_skills' => 'required',
            'purchase_worth' => 'required',
            'support_quality' => 'required',
        ]);

        $data = $request->all();
        $user = auth()->user();

        $webinar = Webinar::where('id', $data['webinar_id'])
            ->where('status', 'active')
            ->first();

        if (!empty($webinar)) {
            if ($webinar->checkUserHasBought($user)) {
                $webinarReview = WebinarReview::where('creator_id', $user->id)
                    ->where('webinar_id', $webinar->id)
                    ->first();

                if (!empty($webinarReview)) {
                    $toastData = [
                        'title' => trans('public.request_failed'),
                        'msg' => trans('public.duplicate_review_for_webinar'),
                        'status' => 'error'
                    ];
                    return back()->with(['toast' => $toastData]);
                }

                $rates = 0;
                $rates += (int)$data['content_quality'];
                $rates += (int)$data['instructor_skills'];
                $rates += (int)$data['purchase_worth'];
                $rates += (int)$data['support_quality'];

                WebinarReview::create([
                    'webinar_id' => $webinar->id,
                    'creator_id' => $user->id,
                    'content_quality' => (int)$data['content_quality'],
                    'instructor_skills' => (int)$data['instructor_skills'],
                    'purchase_worth' => (int)$data['purchase_worth'],
                    'support_quality' => (int)$data['support_quality'],
                    'rates' => $rates > 0 ? $rates / 4 : 0,
                    'description' => $data['description'],
                    'created_at' => time(),
                ]);


                $notifyOptions = [
                    '[c.title]' => $webinar->title,
                    '[student.name]' => $user->full_name,
                    '[rate.count]' => $rates / 4
                ];
                sendNotification('new_rating', $notifyOptions, $webinar->teacher_id);

                $toastData = [
                    'title' => trans('public.request_success'),
                    'msg' => trans('webinars.your_reviews_successfully_submitted_and_waiting_for_admin'),
                    'status' => 'success'
                ];
                return back()->with(['toast' => $toastData]);
            } else {
                $toastData = [
                    'title' => trans('public.request_failed'),
                    'msg' => trans('cart.you_not_purchased_this_course'),
                    'status' => 'error'
                ];
                return back()->with(['toast' => $toastData]);
            }
        }

        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('cart.course_not_found'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData]);
    }

    public function storeReplyComment(Request $request)
    {
        $this->validate($request, [
            'reply' => 'nullable',
        ]);

        $comment = Comment::create([
            'user_id' => auth()->user()->id,
            'comment' => $request->input('reply'),
            'review_id' => $request->input('comment_id'),
            'status' => $request->input('status') ?? Comment::$pending,
            'created_at' => time()
        ]);

        return redirect()->back();
    }

    public function destroy(Request $request, $id)
    {
        if (auth()->check()) {
            $review = WebinarReview::where('id', $id)
                ->where('creator_id', auth()->id())
                ->first();

            if (!empty($review)) {
                $review->delete();

                $toastData = [
                    'title' => trans('public.request_success'),
                    'msg' => trans('webinars.your_review_deleted'),
                    'status' => 'success'
                ];
                return back()->with(['toast' => $toastData]);
            }

            $toastData = [
                'title' => trans('public.request_failed'),
                'msg' => trans('webinars.you_not_access_review'),
                'status' => 'error'
            ];
            return back()->with(['toast' => $toastData]);
        }

        abort(404);
    }
}
