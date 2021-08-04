<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $this->authorize('admin_categories_list');

        $categories = Category::where('parent_id', null)
            ->orderBy('id', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/categories.categories_list_page_title'),
            'categories' => $categories
        ];

        return view('admin.categories.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_categories_create');


        $data = [
            'pageTitle' => trans('admin/main.category_new_page_title'),
        ];

        return view('admin.categories.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_categories_create');

        $this->validate($request, [
            'title' => 'required|min:3|max:128',
            'icon' => 'required',
        ]);

        $category = Category::create([
            'title' => $request->input('title'),
            'icon' => $request->input('icon'),
        ]);

        $hasSubCategories = (!empty($request->get('has_sub')) and $request->get('has_sub') == 'on');
        $this->setSubCategory($category, $request->get('sub_categories'), $hasSubCategories);

        cache()->forget(Category::$cacheKey);

        return redirect('/admin/categories');
    }

    public function edit($id)
    {
        $this->authorize('admin_categories_edit');

        $category = Category::findOrFail($id);
        $subCategories = Category::where('parent_id', $category->id)
            ->orderBy('order', 'asc')
            ->get();

        $data = [
            'pageTitle' => trans('admin/pages/categories.edit_page_title'),
            'category' => $category,
            'subCategories' => $subCategories
        ];

        return view('admin.categories.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_categories_edit');

        $this->validate($request, [
            'title' => 'required|min:3|max:128',
            'icon' => 'required',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'title' => $request->input('title'),
            'icon' => $request->input('icon'),
        ]);

        $hasSubCategories = (!empty($request->get('has_sub')) and $request->get('has_sub') == 'on');
        $this->setSubCategory($category, $request->get('sub_categories'), $hasSubCategories);


        cache()->forget(Category::$cacheKey);

        return redirect('/admin/categories');
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_categories_delete');

        $category = Category::where('id', $id)->first();

        if (!empty($category)) {
            Category::where('parent_id', $category->id)
                ->delete();

            $category->delete();
        }

        cache()->forget(Category::$cacheKey);

        return redirect('/admin/categories');
    }

    public function setSubCategory(Category $category, $subCategories, $hasSubCategories)
    {
        $order = 1;
        $oldIds = [];

        if ($hasSubCategories and !empty($subCategories) and count($subCategories)) {
            foreach ($subCategories as $key => $subCategory) {
                $check = Category::where('id', $key)->first();

                if (is_numeric($key)) {
                    $oldIds[] = $key;
                }

                if (!empty($subCategory['title'])) {
                    if (!empty($check)) {
                        $check->update([
                            'title' => $subCategory['title'],
                            'order' => $order,
                        ]);
                    } else {
                        $new = Category::create([
                            'title' => $subCategory['title'],
                            'parent_id' => $category->id,
                            'order' => $order,
                        ]);

                        $oldIds[] = $new->id;
                    }

                    $order += 1;
                }
            }
        }

        Category::where('parent_id', $category->id)
            ->whereNotIn('id', $oldIds)
            ->delete();

        return true;
    }
}
