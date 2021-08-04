<?php

namespace App\Http\Controllers\Admin;

use App\Exports\NewslettersExport;
use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NewslettersController extends Controller
{
    public function index()
    {
        $this->authorize('admin_newsletters_lists');

        $newsletters = Newsletter::orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.newsletters'),
            'newsletters' => $newsletters
        ];

        return view('admin.newsletters.lists', $data);
    }

    public function delete($id)
    {
        $this->authorize('admin_newsletters_delete');

        $item = Newsletter::findOrFail($id);

        $item->delete();

        return back();
    }

    public function exportExcel()
    {
        $this->authorize('admin_newsletters_export_excel');

        $newsletters = Newsletter::orderBy('created_at', 'desc')
            ->get();

        $newslettersExport = new NewslettersExport($newsletters);

        return Excel::download($newslettersExport, trans('admin/main.newsletters') . '.xlsx');
    }
}
