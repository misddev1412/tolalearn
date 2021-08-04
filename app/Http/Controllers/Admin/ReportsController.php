<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Models\Setting;
use App\Models\WebinarReport;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function reasons()
    {
        $this->authorize('admin_report_reasons');

        $value = [];

        $settings = Setting::where('name', 'report_reasons')->first();
        if (!empty($settings) and !empty($settings->value)) {
            $value = json_decode($settings->value, true);
        }


        $data = [
            'pageTitle' => trans('admin/pages/setting.report_reasons'),
            'value' => $value,
        ];


        return view('admin.reports.reasons', $data);
    }

    public function storeReasons(Request $request)
    {
        $this->authorize('admin_report_reasons');

        $name = 'report_reasons';

        $values = $request->get('value', null);

        if (!empty($values)) {

            $values = array_filter($values, function ($val) {
                if (is_array($val)) {
                    return array_filter($val);
                } else {
                    return !empty($val);
                }
            });

            $values = json_encode($values);
            $values = str_replace('record', rand(1, 600), $values);

            Setting::updateOrCreate(
                ['name' => $name],
                [
                    'value' => $values,
                    'updated_at' => time(),
                ]
            );

            cache()->forget($name);
        }

        return back();
    }

    public function webinarsReports()
    {
        $this->authorize('admin_webinar_reports');

        $reports = WebinarReport::with(['user' => function ($query) {
            $query->select('id', 'full_name');
        }, 'webinar' => function ($query) {
            $query->select('id', 'title', 'slug');
        }])->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/comments.classes_reports'),
            'reports' => $reports
        ];

        return view('admin.webinars.reports', $data);
    }

    public function delete($id)
    {
        $this->authorize('admin_webinar_reports_delete');

        $report = WebinarReport::findOrFail($id);

        $report->delete();

        return redirect()->back();
    }
}
