<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index()
    {
        $this->authorize('admin_pages_list');

        $pages = Page::orderBy('created_at', 'desc')->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/setting.pages'),
            'pages' => $pages
        ];

        return view('admin.pages.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_pages_create');

        $data = [
            'pageTitle' => trans('admin/pages/setting.new_pages')
        ];

        return view('admin.pages.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_pages_create');

        $this->validate($request, [
            'name' => 'required',
            'link' => 'required|unique:pages,link',
            'title' => 'required',
            'seo_description' => 'nullable|string|max:255',
            'content' => 'required',
        ]);

        $data = $request->all();

        $firstCharacter = substr($data['link'], 0, 1);
        if ($firstCharacter !== '/') {
            $data['link'] = '/' . $data['link'];
        }

        unset($data['_token']);

        $data['created_at'] = time();

        Page::create($data);

        return redirect('/admin/pages');
    }

    public function edit($id)
    {
        $this->authorize('admin_pages_edit');

        $page = Page::findOrFail($id);

        $data = [
            'pageTitle' => trans('admin/pages/setting.edit_pages') . $page->name,
            'page' => $page
        ];

        return view('admin.pages.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_pages_edit');

        $page = Page::findOrFail($id);

        $this->validate($request, [
            'name' => 'required',
            'link' => 'required|unique:pages,link,' . $page->id,
            'title' => 'required',
            'seo_description' => 'nullable|string|max:255',
            'content' => 'required',
        ]);

        $data = $request->all();

        $firstCharacter = substr($data['link'], 0, 1);
        if ($firstCharacter !== '/') {
            $data['link'] = '/' . $data['link'];
        }

        unset($data['_token']);

        $data['robot'] = (!empty($data['robot']) and $data['robot'] == '1');

        $page->update($data);

        return redirect('/admin/pages');
    }

    public function delete($id)
    {
        $this->authorize('admin_pages_delete');

        $page = Page::findOrFail($id);

        $page->delete();

        return redirect('/admin/pages');
    }

    public function statusTaggle($id)
    {
        $this->authorize('admin_pages_toggle');

        $page = Page::findOrFail($id);

        $page->update([
            'status' => ($page->status == 'draft') ? 'publish' : 'draft'
        ]);

        return redirect('/admin/pages');
    }
}
