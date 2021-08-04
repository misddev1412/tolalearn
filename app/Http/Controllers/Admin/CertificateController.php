<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CertificatesExport;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\User;
use App\Models\Quiz;
use App\Models\CertificateTemplate;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_certificate_list');

        $query = Certificate::query();

        $query = $this->filters($query, $request);

        $certificates = $query->with(
            [
                'quiz' => function ($query) {
                    $query->with('webinar');
                },
                'student',
                'quizzesResult'
            ]
        )->orderBy('created_at', 'desc')
            ->paginate(10);


        $data = [
            'pageTitle' => trans('admin/main.certificate_list_page_title'),
            'certificates' => $certificates,
            'student' => $filters['student'] ?? null,
            'instructor' => $filters['instructor'] ?? null,
            'quiz_title' => $filters['quiz_title'] ?? null,
        ];

        $teacher_ids = $request->get('teacher_ids');
        $student_ids = $request->get('student_ids');

        if (!empty($teacher_ids)) {
            $data['teachers'] = User::select('id', 'full_name')
                ->whereIn('id', $teacher_ids)->get();
        }

        if (!empty($student_ids)) {
            $data['students'] = User::select('id', 'full_name')
                ->whereIn('id', $student_ids)->get();
        }

        return view('admin.certificates.lists', $data);
    }

    private function filters($query, $request)
    {
        $filters = $request->all();

        if (!empty($filters['student_ids'])) {
            $query->whereIn('student_id', $filters['student_ids']);
        }

        if (!empty($filters['teacher_ids'])) {
            $quizzes = Quiz::whereIn('creator_id', $filters['teacher_ids'])->pluck('id')->toArray();

            if ($quizzes and is_array($quizzes)) {
                $query->whereIn('quiz_id', $quizzes);
            }
        }

        if (!empty($filters['quiz_title'])) {
            $quizzes = Quiz::where('title', 'like', '%' . $filters['quiz_title'] . '%')->pluck('id')->toArray();
            $query->whereIn('quiz_id', $quizzes);
        }

        return $query;
    }

    public function CertificatesTemplatesList(Request $request)
    {
        $this->authorize('admin_certificate_template_list');

        $templates = CertificateTemplate::orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.certificate_templates_list_page_title'),
            'templates' => $templates,
        ];

        return view('admin.certificates.templates', $data);
    }

    public function CertificatesNewTemplate()
    {
        $this->authorize('admin_certificate_template_create');

        $data = [
            'pageTitle' => trans('admin/main.certificate_new_template_page_title'),
        ];

        return view('admin.certificates.new_templates', $data);
    }

    public function CertificatesTemplateStore(Request $request, $template_id = null)
    {
        $this->authorize('admin_certificate_template_create');

        $rules = [
            'title' => 'required',
            'image' => 'required',
            'body' => 'required',
            'position_x' => 'required',
            'position_y' => 'required',
            'font_size' => 'required',
            'text_color' => 'required',
        ];
        $this->validate($request, $rules);

        $status = $request->get('status', 'draft');

        $data = [
            'title' => $request->get('title'),
            'image' => $request->get('image'),
            'body' => $request->get('body'),
            'position_x' => $request->get('position_x'),
            'position_y' => $request->get('position_y'),
            'font_size' => $request->get('font_size'),
            'text_color' => $request->get('text_color'),
            'status' => $status,
        ];

        if ($status == 'publish') { // set draft for other templates
            CertificateTemplate::where('status', 'publish')
                ->update([
                    'status' => 'draft'
                ]);
        }

        if (!empty($template_id)) {
            $data['updated_at'] = time();
            $template = CertificateTemplate::findOrFail($template_id);
            $template->update($data);
        } else {
            $data['created_at'] = time();
            CertificateTemplate::create($data);
        }

        return redirect('/admin/certificates/templates');
    }

    public function CertificatesTemplatePreview(Request $request)
    {
        $this->authorize('admin_certificate_template_create');

        $data = [
            'image' => $request->get('image'),
            'body' => $request->get('body'),
            'position_x' => (int)$request->get('position_x', 120),
            'position_y' => (int)$request->get('position_y', 100),
            'font_size' => (int)$request->get('font_size', 26),
            'text_color' => $request->get('text_color', '#e1e1e1'),
        ];

        $body = str_replace('[student]', 'student name', $data['body']);
        $body = str_replace('[course]', 'course name', $body);
        $body = str_replace('[grade]', 'xx', $body);
        $body = str_replace('[certificate_id]', 'xx', $body);

        $imgPath = public_path($data['image']);
        $img = Image::make($imgPath);

        $img->text($body, $data['position_x'], $data['position_y'], function ($font) use ($data) {
            $font->file(public_path('assets/default/fonts/vazir/Vazir-Medium.ttf'));
            $font->size($data['font_size']);
            $font->color($data['text_color']);
        });
        return $img->response('png');
    }

    public function CertificatesTemplatesEdit($template_id)
    {
        $this->authorize('admin_certificate_template_edit');

        $template = CertificateTemplate::findOrFail($template_id);

        $data = [
            'pageTitle' => trans('admin/main.certificate_template_edit_page_title'),
            'template' => $template
        ];
        return view('admin.certificates.new_templates', $data);
    }

    public function CertificatesTemplatesDelete($template_id)
    {
        $this->authorize('admin_certificate_template_delete');

        $template = CertificateTemplate::findOrFail($template_id);

        $template->delete();

        return redirect('/admin/certificates/templates');
    }

    public function CertificatesDownload($id)
    {
        $certificate = Certificate::findOrFail($id);

        if (!empty($certificate->file)) {
            if (file_exists(public_path($certificate->file))) {
                return response()->download(public_path($certificate->file));
            }
        }

        abort(404);
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_certificate_export_excel');

        $query = Certificate::query();

        $query = $this->filters($query, $request);

        $certificates = $query->with(
            [
                'quiz' => function ($query) {
                    $query->with('webinar');
                },
                'student',
                'quizzesResult'
            ]
        )->orderBy('created_at', 'desc')
            ->get();

        $export = new CertificatesExport($certificates);

        return Excel::download($export, 'certificates.xlsx');
    }
}
