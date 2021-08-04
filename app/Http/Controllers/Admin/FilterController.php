<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Filter;
use App\Models\FilterOption;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function index()
    {
        $this->authorize('admin_filters_list');

        $filters = Filter::with('category')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.filters_list_page_title'),
            'filters' => $filters
        ];

        return view('admin.filters.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_filters_create');

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $data = [
            'pageTitle' => trans('admin/main.filter_new_page_title'),
            'categories' => $categories
        ];

        return view('admin.filters.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_filters_create');

        $this->validate($request, [
            'title' => 'required|min:3|max:128',
            'category_id' => 'required|exists:categories,id',
        ]);

        $filter = Filter::create([
            'title' => $request->input('title'),
            'category_id' => $request->input('category_id'),
        ]);

        if ($request->input('sub_filters')) {
            $this->setSubFilters($filter, $request->input('sub_filters'));
        }

        return redirect('/admin/filters');
    }

    public function edit($id)
    {
        $this->authorize('admin_filters_edit');

        $filter = Filter::findOrFail($id);
        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $filterOptions = FilterOption::where('filter_id', $filter->id)
            ->orderBy('order', 'asc')
            ->get();

        $data = [
            'pageTitle' => trans('admin/main.admin_filters_edit'),
            'filter' => $filter,
            'categories' => $categories,
            'filterOptions' => $filterOptions,
        ];

        return view('admin.filters.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_filters_edit');

        $this->validate($request, [
            'title' => 'required|min:3|max:128',
            'category_id' => 'required|exists:categories,id',
        ]);

        $filter = Filter::findOrFail($id);
        $filter->update([
            'title' => $request->input('title'),
            'category_id' => $request->input('category_id'),
        ]);

        if ($request->input('sub_filters')) {
            $this->setSubFilters($filter, $request->input('sub_filters'));
        }

        return redirect('/admin/filters');
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_filters_delete');

        Filter::find($id)->delete();

        return redirect('/admin/filters');
    }

    public function setSubFilters(Filter $filter, $filterOptions)
    {
        $order = 1;
        foreach ($filterOptions as $key => $filterOption) {
            if (!empty($filterOption['title'])) {
                $oldFilterOption = FilterOption::where('filter_id', $filter->id)
                    ->where('id', $key)
                    ->first();

                if (!empty($oldFilterOption)) {
                    $oldFilterOption->update([
                        'title' => $filterOption['title'],
                        'order' => $order,
                    ]);
                } else {
                    FilterOption::create([
                        'title' => $filterOption['title'],
                        'filter_id' => $filter->id,
                        'order' => $order,
                    ]);
                }

                $order += 1;
            }
        }
    }

    public function getByCategoryId($categoryId)
    {
        $filters = Filter::select('id', 'title')
            ->where('category_id', $categoryId)
            ->with([
                'options'  => function ($query) {
                    $query->orderBy('order', 'asc');
                },
            ])
            ->get();

        return response()->json([
            'filters' => $filters,
        ], 200);
    }
}
