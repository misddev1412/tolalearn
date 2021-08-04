<?php

namespace App\Http\Controllers\Admin;

use App\Exports\FeatureWebinarsExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FeatureWebinar;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FeatureWebinarsControllers extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_feature_webinars');

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $query = FeatureWebinar::with([
            'webinar' => function ($query) {
                $query->select(['id', 'teacher_id', 'category_id', 'title', 'slug', 'status'])
                    ->with(['category', 'teacher' => function ($query) {
                        $query->select(['id', 'full_name']);
                    }]);
            }
        ]);

        $query = $this->filters($query, $request);

        $features = $query->orderBy('updated_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/webinars.feature_webinars'),
            'categories' => $categories,
            'features' => $features,
        ];

        return view('admin.webinars.feature.lists', $data);
    }

    private function filters($query, $request)
    {
        $page = $request->get('page', null);
        $status = $request->get('status', null);
        $category_id = $request->get('category_id', null);
        $webinar_title = $request->get('webinar_title', null);

        if (!empty($page)) {
            $query->where('page', $page);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($category_id)) {
            $query->whereHas('webinar', function ($q) use ($category_id) {
                $q->whereHas('category', function ($q) use ($category_id) {
                    $q->where('id', $category_id);
                });
            });
        }

        if (!empty($webinar_title)) {
            $query->whereHas('webinar', function ($q) use ($webinar_title) {
                $q->where('title', 'like', '%' . $webinar_title . '%');
            });
        }

        return $query;
    }

    public function create()
    {
        $this->authorize('admin_feature_webinars_create');

        $data = [
            'pageTitle' => trans('admin/pages/webinars.feature_webinars'),
        ];

        return view('admin.webinars.feature.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_feature_webinars_create');

        $this->validate($request, [
            'webinar_id' => 'required|unique:feature_webinars,webinar_id'
        ]);

        FeatureWebinar::create([
            'webinar_id' => $request->get('webinar_id'),
            'page' => $request->get('page'),
            'description' => $request->get('description'),
            'status' => $request->get('status', 'pending'),
            'updated_at' => time()
        ]);

        return redirect('/admin/webinars/features');
    }

    public function toggle($feature_id, $toggle)
    {
        $this->authorize('admin_feature_webinars');

        $feature = FeatureWebinar::findOrFail($feature_id);

        if (in_array($toggle, ['pending', 'publish'])) {
            $feature->update([
                'status' => $toggle
            ]);
        } elseif ($toggle == 'delete') {
            $feature->delete();
        }

        return back();
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_feature_webinars_export_excel');

        $query = FeatureWebinar::with([
            'webinar' => function ($query) {
                $query->select(['id', 'teacher_id', 'category_id', 'title', 'slug', 'status'])
                    ->with(['category', 'teacher' => function ($query) {
                        $query->select(['id', 'full_name']);
                    }]);
            }
        ]);

        $query = $this->filters($query, $request);

        $features = $query->orderBy('updated_at', 'desc')
            ->get();

        $export = new FeatureWebinarsExport($features);

        return Excel::download($export, 'feature_webinars.xlsx');
    }
}
