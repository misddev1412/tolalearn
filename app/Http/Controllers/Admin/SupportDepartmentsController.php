<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportDepartment;
use Illuminate\Http\Request;

class SupportDepartmentsController extends Controller
{
    public function index()
    {
        $this->authorize('admin_support_departments');

        $departments = SupportDepartment::withCount('supports')
            ->orderBy('created_at','desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.support_departments_title'),
            'departments' => $departments
        ];

        return view('admin.supports.departments', $data);
    }

    public function create()
    {
        $this->authorize('admin_support_department_create');

        $data = [
            'pageTitle' => trans('admin/pages/users.new_department'),
        ];

        return view('admin.supports.department_create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_support_department_create');

        $this->validate($request, [
            'title' => 'required|string|min:2'
        ]);

        SupportDepartment::create([
            'title' => $request->get('title'),
            'created_at' => time(),
        ]);

        return redirect('/admin/supports/departments');
    }

    public function edit($id)
    {
        $this->authorize('admin_support_departments_edit');

        $department = SupportDepartment::findOrFail($id);

        $data = [
            'pageTitle' => trans('admin/pages/users.edit_department'),
            'department' => $department
        ];

        return view('admin.supports.department_create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_support_departments_edit');

        $this->validate($request, [
            'title' => 'required|string|min:2'
        ]);

        $department = SupportDepartment::findOrFail($id);

        $department->update([
            'title' => $request->get('title'),
            'created_at' => time(),
        ]);

        return redirect('/admin/supports/departments');
    }

    public function delete($id)
    {
        $this->authorize('admin_support_departments_delete');

        $department = SupportDepartment::findOrFail($id);

        $department->delete();

        return redirect('/admin/supports/departments');
    }
}
