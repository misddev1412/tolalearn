<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Category;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_blog_lists');

        $query = Blog::query();

        $blog = $this->filters($query, $request)
            ->with(['category', 'author' => function ($query) {
                $query->select('id', 'full_name');
            }])
            ->withCount('comments')
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $blogCategories = BlogCategory::all();
        $adminRoleIds = Role::where('is_admin', true)->pluck('id')->toArray();
        $authors = User::select('id', 'full_name', 'role_id')->whereIn('role_id', $adminRoleIds)->get();

        $data = [
            'pageTitle' => trans('admin/pages/blog.blog'),
            'blog' => $blog,
            'blogCategories' => $blogCategories,
            'authors' => $authors,
        ];

        return view('admin.blog.lists', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $title = $request->get('title', null);
        $category_id = $request->get('category_id', null);
        $author_id = $request->get('author_id', null);
        $status = $request->get('status', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');


        if (!empty($title)) {
            $query->where('title', 'like', '%' . $title . '%');
        }

        if (!empty($category_id)) {
            $query->where('category_id', $category_id);
        }

        if (!empty($author_id)) {
            $query->where('author_id', $author_id);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function create()
    {
        $this->authorize('admin_blog_create');

        $categories = BlogCategory::all();

        $data = [
            'pageTitle' => trans('admin/pages/blog.create_blog'),
            'categories' => $categories
        ];

        return view('admin.blog.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_blog_create');

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'category_id' => 'required|numeric',
            'image' => 'required|string',
            'description' => 'required|string',
            'content' => 'required|string',
        ]);

        $data = $request->all();

        Blog::create([
            'title' => $data['title'],
            'category_id' => $data['category_id'],
            'author_id' => auth()->id(),
            'image' => $data['image'],
            'description' => $data['description'],
            'meta_description' => $data['meta_description'],
            'content' => $data['content'],
            'enable_comment' => (!empty($data['enable_comment']) and $data['enable_comment'] == 'on'),
            'status' => (!empty($data['status']) and $data['status'] == 'on') ? 'publish' : 'pending',
            'created_at' => time(),
        ]);

        return redirect('/admin/blog');
    }

    public function edit($post_id)
    {
        $this->authorize('admin_blog_edit');

        $post = Blog::findOrFail($post_id);
        $categories = BlogCategory::all();

        $data = [
            'pageTitle' => trans('admin/pages/blog.create_blog'),
            'categories' => $categories,
            'post' => $post,
        ];

        return view('admin.blog.create', $data);
    }

    public function update(Request $request, $post_id)
    {
        $this->authorize('admin_blog_edit');

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'category_id' => 'required|numeric',
            'image' => 'required|string',
            'description' => 'required|string',
            'content' => 'required|string',
        ]);

        $data = $request->all();
        $post = Blog::findOrFail($post_id);

        $post->slug = null; // regenerate slug in model

        $post->update([
            'title' => $data['title'],
            'category_id' => $data['category_id'],
            'author_id' => auth()->id(),
            'image' => $data['image'],
            'description' => $data['description'],
            'meta_description' => $data['meta_description'],
            'content' => $data['content'],
            'enable_comment' => (!empty($data['enable_comment']) and $data['enable_comment'] == 'on'),
            'status' => (!empty($data['status']) and $data['status'] == 'on') ? 'publish' : 'pending',
            'updated_at' => time(),
        ]);

        return redirect('/admin/blog');
    }

    public function delete($post_id)
    {
        $this->authorize('admin_blog_delete');

        $post = Blog::findOrFail($post_id);

        $post->delete();

        return redirect('/admin/blog');
    }

    public function search(Request $request)
    {
        $term = $request->get('term');
        $blog = Blog::select('id', 'title')
            ->where('title', 'like', "%$term%")
            ->get();

        return response()->json($blog, 200);
    }
}
