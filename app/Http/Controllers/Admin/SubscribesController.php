<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscribe;
use Illuminate\Http\Request;

class SubscribesController extends Controller
{
    public function index()
    {
        $this->authorize('admin_subscribe_list');

        $subscribes = Subscribe::with([
            'sales' => function ($query) {
                $query->whereNull('refund_at');
            }
        ])->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/financial.subscribes'),
            'subscribes' => $subscribes
        ];

        return view('admin.financial.subscribes.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_subscribe_create');

        $data = [
            'pageTitle' => trans('admin/pages/financial.new_subscribe'),
        ];

        return view('admin.financial.subscribes.new', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_subscribe_create');

        $this->validate($request, [
            'title' => 'required|string',
            'usable_count' => 'required|numeric',
            'days' => 'required|numeric',
            'price' => 'required|numeric',
            'icon' => 'required|string',
        ]);

        $data = $request->all();

        Subscribe::create([
            'title' => $data['title'],
            'usable_count' => $data['usable_count'],
            'days' => $data['days'],
            'price' => $data['price'],
            'icon' => $data['icon'],
            'description' => !empty($data['description']) ? $data['description'] : null,
            'is_popular' => !empty($data['is_popular']) ? $data['is_popular'] : false,
            'created_at' => time(),
        ]);

        return redirect('/admin/financial/subscribes');
    }

    public function edit($id)
    {
        $this->authorize('admin_subscribe_edit');

        $subscribe = Subscribe::findOrFail($id);

        $data = [
            'pageTitle' => trans('admin/pages/financial.new_subscribe'),
            'subscribe' => $subscribe
        ];

        return view('admin.financial.subscribes.new', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_subscribe_edit');

        $this->validate($request, [
            'title' => 'required|string',
            'usable_count' => 'required|numeric',
            'days' => 'required|numeric',
            'price' => 'required|numeric',
            'icon' => 'required|string',
        ]);

        $data = $request->all();
        $subscribe = Subscribe::findOrFail($id);

        $subscribe->update([
            'title' => $data['title'],
            'usable_count' => $data['usable_count'],
            'days' => $data['days'],
            'price' => $data['price'],
            'icon' => $data['icon'],
            'description' => !empty($data['description']) ? $data['description'] : null,
            'is_popular' => !empty($data['is_popular']) ? $data['is_popular'] : false,
            'created_at' => time(),
        ]);

        return redirect('/admin/financial/subscribes');
    }

    public function delete($id)
    {
        $this->authorize('admin_subscribe_delete');

        $promotion = Subscribe::findOrFail($id);

        $promotion->delete();

        return redirect('/admin/financial/subscribes');
    }
}
