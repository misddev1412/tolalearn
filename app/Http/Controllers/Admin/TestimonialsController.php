<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialsController extends Controller
{
    public function index()
    {
        $this->authorize('admin_testimonials_list');

        $testimonials = Testimonial::query()->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/comments.testimonials'),
            'testimonials' => $testimonials
        ];

        return view('admin.testimonials.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_testimonials_create');

        $data = [
            'pageTitle' => trans('admin/pages/comments.new_testimonial'),
        ];

        return view('admin.testimonials.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_testimonials_create');

        $this->validate($request, [
            'user_avatar' => 'nullable|string',
            'user_name' => 'required|string',
            'user_bio' => 'required|string',
            'rate' => 'required|integer|between:0,5',
            'comment' => 'required|string',
        ]);

        $data = $request->all();
        unset($data['files'], $data['_token']);

        if (empty($data['user_avatar'])) {
            $data['user_avatar'] = getPageBackgroundSettings('user_avatar');
        }

        $data['created_at'] = time();

        Testimonial::create($data);

        return redirect('/admin/testimonials');
    }


    public function edit($id)
    {
        $this->authorize('admin_testimonials_edit');

        $testimonial = Testimonial::findOrFail($id);

        $data = [
            'pageTitle' => trans('admin/pages/comments.edit_testimonial'),
            'testimonial' => $testimonial
        ];

        return view('admin.testimonials.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_testimonials_edit');

        $this->validate($request, [
            'user_avatar' => 'nullable|string',
            'user_name' => 'required|string',
            'user_bio' => 'required|string',
            'rate' => 'required|integer|between:0,5',
            'comment' => 'required|string',
        ]);

        $testimonial = Testimonial::findOrFail($id);

        $data = $request->all();
        unset($data['files'], $data['_token']);

        if (empty($data['user_avatar'])) {
            $data['user_avatar'] = getPageBackgroundSettings('user_avatar');
        }


        $testimonial->update($data);

        return redirect('/admin/testimonials');
    }

    public function delete($id)
    {
        $this->authorize('admin_testimonials_delete');

        $testimonial = Testimonial::findOrFail($id);

        $testimonial->delete();

        return redirect('/admin/testimonials');
    }
}
