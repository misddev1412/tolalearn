<?php

namespace App\Http\Controllers\Admin;

use App\Exports\WebinarsExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Quiz;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SpecialOffer;
use App\Models\Tag;
use App\Models\Ticket;
use App\Models\WebinarFilterOption;
use App\Models\WebinarPartnerTeacher;
use App\Models\WebinarReport;
use App\User;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class WebinarController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_webinars_list');

        $type = $request->get('type', 'webinar');
        $query = Webinar::where('webinars.type', $type);

        $totalWebinars = $query->count();
        $totalPendingWebinars = deepClone($query)->where('webinars.status', 'pending')->count();
        $totalDurations = deepClone($query)->sum('duration');
        $totalSales = deepClone($query)->join('sales', 'webinars.id', '=', 'sales.webinar_id')
            ->select(DB::raw('count(sales.webinar_id) as sales_count'))
            ->whereNotNull('sales.webinar_id')
            ->whereNull('sales.refund_at')
            ->first();

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $inProgressWebinars = 0;
        if ($type == 'webinar') {
            $inProgressWebinars = $this->getInProgressWebinarsCount();
        }

        $query = $this->filterWebinar($query, $request)
            ->with([
                'category',
                'teacher' => function ($qu) {
                    $qu->select('id', 'full_name');
                },
                'sales' => function ($query) {
                    $query->whereNull('refund_at');
                }
            ]);

        $webinars = $query->paginate(10);

        if ($request->get('status', null) == 'active_finished') {
            foreach ($webinars as $key => $webinar) {
                if ($webinar->last_date > time()) { // is in progress
                    unset($webinars[$key]);
                }
            }
        }

        $data = [
            'pageTitle' => trans('admin/pages/webinars.webinars_list_page_title'),
            'webinars' => $webinars,
            'totalWebinars' => $totalWebinars,
            'totalPendingWebinars' => $totalPendingWebinars,
            'totalDurations' => $totalDurations,
            'totalSales' => !empty($totalSales) ? $totalSales->sales_count : 0,
            'categories' => $categories,
            'inProgressWebinars' => $inProgressWebinars,
            'classesType' => $type,
        ];

        $teacher_ids = $request->get('teacher_ids', null);
        if (!empty($teacher_ids)) {
            $data['teachers'] = User::select('id', 'full_name')->whereIn('id', $teacher_ids)->get();
        }

        return view('admin.webinars.lists', $data);
    }

    private function filterWebinar($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $title = $request->get('title', null);
        $teacher_ids = $request->get('teacher_ids', null);
        $category_id = $request->get('category_id', null);
        $status = $request->get('status', null);
        $sort = $request->get('sort', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($title)) {
            $query->where('title', 'like', '%' . $title . '%');
        }

        if (!empty($teacher_ids) and count($teacher_ids)) {
            $query->whereIn('teacher_id', $teacher_ids);
        }

        if (!empty($category_id)) {
            $query->where('category_id', $category_id);
        }

        if (!empty($status)) {
            $time = time();

            switch ($status) {
                case 'active_not_conducted':
                    $query->where('status', 'active')
                        ->where('start_date', '>', $time);
                    break;
                case 'active_in_progress':
                    $query->where('status', 'active')
                        ->where('start_date', '<=', $time)
                        ->join('sessions', 'webinars.id', '=', 'sessions.webinar_id')
                        ->select('webinars.*', 'sessions.date', DB::raw('max(`date`) as last_date'))
                        ->groupBy('sessions.webinar_id')
                        ->where('sessions.date', '>', $time);
                    break;
                case 'active_finished':
                    $query->where('status', 'active')
                        ->where('start_date', '<=', $time)
                        ->join('sessions', 'webinars.id', '=', 'sessions.webinar_id')
                        ->select('webinars.*', 'sessions.date', DB::raw('max(`date`) as last_date'))
                        ->groupBy('sessions.webinar_id');
                    break;
                default:
                    $query->where('status', $status);
                    break;
            }
        }

        if (!empty($sort)) {
            switch ($sort) {
                case 'has_discount':
                    $now = time();
                    $webinarIdsHasDiscount = [];

                    $tickets = Ticket::where('start_date', '<', $now)
                        ->where('end_date', '>', $now)
                        ->get();

                    foreach ($tickets as $ticket) {
                        if ($ticket->isValid()) {
                            $webinarIdsHasDiscount[] = $ticket->webinar_id;
                        }
                    }

                    $specialOffersWebinarIds = SpecialOffer::where('status', 'active')
                        ->where('from_date', '<', $now)
                        ->where('to_date', '>', $now)
                        ->pluck('webinar_id')
                        ->toArray();

                    $webinarIdsHasDiscount = array_merge($specialOffersWebinarIds, $webinarIdsHasDiscount);

                    $query->whereIn('id', $webinarIdsHasDiscount)
                        ->orderBy('created_at', 'desc');
                    break;
                case 'sales_asc':
                    $query->join('sales', 'webinars.id', '=', 'sales.webinar_id')
                        ->select('webinars.*', 'sales.webinar_id', 'sales.refund_at', DB::raw('count(sales.webinar_id) as sales_count'))
                        ->whereNotNull('sales.webinar_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('sales.webinar_id')
                        ->orderBy('sales_count', 'asc');
                    break;
                case 'sales_desc':
                    $query->join('sales', 'webinars.id', '=', 'sales.webinar_id')
                        ->select('webinars.*', 'sales.webinar_id', 'sales.refund_at', DB::raw('count(sales.webinar_id) as sales_count'))
                        ->whereNotNull('sales.webinar_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('sales.webinar_id')
                        ->orderBy('sales_count', 'desc');
                    break;

                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;

                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;

                case 'income_asc':
                    $query->join('sales', 'webinars.id', '=', 'sales.webinar_id')
                        ->select('webinars.*', 'sales.webinar_id', 'sales.total_amount', 'sales.refund_at', DB::raw('(sum(sales.total_amount) - (sum(sales.tax) + sum(sales.commission))) as amounts'))
                        ->whereNotNull('sales.webinar_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('sales.webinar_id')
                        ->orderBy('amounts', 'asc');
                    break;

                case 'income_desc':
                    $query->join('sales', 'webinars.id', '=', 'sales.webinar_id')
                        ->select('webinars.*', 'sales.webinar_id', 'sales.total_amount', 'sales.refund_at', DB::raw('(sum(sales.total_amount) - (sum(sales.tax) + sum(sales.commission))) as amounts'))
                        ->whereNotNull('sales.webinar_id')
                        ->whereNull('sales.refund_at')
                        ->groupBy('sales.webinar_id')
                        ->orderBy('amounts', 'desc');
                    break;

                case 'created_at_asc':
                    $query->orderBy('created_at', 'asc');
                    break;

                case 'created_at_desc':
                    $query->orderBy('created_at', 'desc');
                    break;

                case 'updated_at_asc':
                    $query->orderBy('updated_at', 'asc');
                    break;

                case 'updated_at_desc':
                    $query->orderBy('updated_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }


        return $query;
    }

    private function getInProgressWebinarsCount()
    {
        $count = 0;
        $webinars = Webinar::where('type', 'webinar')
            ->where('status', 'active')
            ->where('start_date', '<=', time())
            ->whereHas('sessions')
            ->get();

        foreach ($webinars as $webinar) {
            if ($webinar->isProgressing()) {
                $count += 1;
            }
        }

        return $count;
    }

    public function create()
    {
        $this->authorize('admin_webinars_create');

        $teachers = User::where('role_name', Role::$teacher)->get();
        $categories = Category::where('parent_id', null)->get();

        $data = [
            'pageTitle' => trans('admin/main.webinar_new_page_title'),
            'teachers' => $teachers,
            'categories' => $categories
        ];

        return view('admin.webinars.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_webinars_create');

        $this->validate($request, [
            'type' => 'required|in:webinar,course,text_lesson',
            'title' => 'required|max:255',
            'thumbnail' => 'required',
            'image_cover' => 'required',
            'description' => 'required',
            'teacher_id' => 'required|exists:users,id',
            'category_id' => 'required',
            'duration' => 'required',
            'start_date' => 'required_if:type,webinar',
            'capacity' => 'required_if:type,webinar',
        ]);

        $data = $request->all();

        $webinar = Webinar::create([
            'type' => $data['type'],
            'title' => $data['title'],
            'teacher_id' => $data['teacher_id'],
            'creator_id' => $data['teacher_id'],
            'seo_description' => $data['seo_description'],
            'thumbnail' => $data['thumbnail'],
            'image_cover' => $data['image_cover'],
            'video_demo' => $data['video_demo'],
            'description' => $data['description'],
            'capacity' => $data['capacity'] ?? null,
            'start_date' => (!empty($data['start_date']) and $data['type'] == Webinar::$webinar) ? strtotime($data['start_date']) : null,
            'duration' => $data['duration'] ?? null,
            'support' => !empty($data['support']) ? true : false,
            'downloadable' => !empty($data['downloadable']) ? true : false,
            'partner_instructor' => !empty($data['partner_instructor']) ? true : false,
            'subscribe' => !empty($data['subscribe']) ? true : false,
            'price' => $data['price'],
            'category_id' => $data['category_id'],
            'status' => Webinar::$pending,
            'created_at' => time(),
        ]);

        $filters = $request->get('filters', null);
        if (!empty($filters) and is_array($filters)) {
            WebinarFilterOption::where('webinar_id', $webinar->id)->delete();
            foreach ($filters as $filter) {
                WebinarFilterOption::create([
                    'webinar_id' => $webinar->id,
                    'filter_option_id' => $filter
                ]);
            }
        }

        if (!empty($request->get('tags'))) {
            $tags = explode(',', $request->get('tags'));
            Tag::where('webinar_id', $webinar->id)->delete();

            foreach ($tags as $tag) {
                Tag::create([
                    'webinar_id' => $webinar->id,
                    'title' => $tag,
                ]);
            }
        }

        if (!empty($request->get('partner_instructor')) and !empty($request->get('partners'))) {
            WebinarPartnerTeacher::where('webinar_id', $webinar->id)->delete();

            foreach ($request->get('partners') as $partnerId) {
                WebinarPartnerTeacher::create([
                    'webinar_id' => $webinar->id,
                    'teacher_id' => $partnerId,
                ]);
            }
        }


        return redirect('/admin/webinars/' . $webinar->id . '/edit');
    }

    public function edit($id)
    {
        $this->authorize('admin_webinars_edit');

        $webinar = Webinar::where('id', $id)
            ->with([
                'tickets',
                'sessions',
                'files',
                'faqs',
                'category' => function ($query) {
                    $query->with(['filters' => function ($query) {
                        $query->with('options');
                    }]);
                },
                'filterOptions',
                'prerequisites',
                'quizzes',
                'webinarPartnerTeacher' => function ($query) {
                    $query->with(['teacher' => function ($query) {
                        $query->select('id', 'full_name');
                    }]);
                },
                'tags',
                'textLessons'
            ])
            ->first();

        if (empty($webinar)) {
            abort(404);
        }

        $teachers = User::where('role_name', Role::$teacher)->get();
        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $teacherQuizzes = Quiz::where('webinar_id', null)
            ->where('creator_id', $webinar->teacher_id)
            ->get();

        $tags = $webinar->tags->pluck('title')->toArray();

        $data = [
            'pageTitle' => trans('admin/main.edit') . ' | ' . $webinar->title,
            'teachers' => $teachers,
            'categories' => $categories,
            'webinar' => $webinar,
            'webinarCategoryFilters' => $webinar->category->filters,
            'webinarFilterOptions' => $webinar->filterOptions->pluck('filter_option_id')->toArray(),
            'tickets' => $webinar->tickets,
            'sessions' => $webinar->sessions,
            'files' => $webinar->files,
            'textLessons' => $webinar->textLessons,
            'faqs' => $webinar->faqs,
            'teacherQuizzes' => $teacherQuizzes,
            'prerequisites' => $webinar->prerequisites,
            'webinarQuizzes' => $webinar->quizzes,
            'webinarPartnerTeacher' => $webinar->webinarPartnerTeacher->keyby('teacher_id'),
            'webinarTags' => $tags,
        ];

        return view('admin.webinars.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');
        $data = $request->all();

        $webinar = Webinar::find($id);
        $isDraft = (!empty($data['draft']) and $data['draft'] == 1);
        $reject = (!empty($data['draft']) and $data['draft'] == 'reject');
        $publish = (!empty($data['draft']) and $data['draft'] == 'publish');

        $rules = [
            'type' => 'required|in:webinar,course,text_lesson',
            'title' => 'required|max:255',
            'thumbnail' => 'required',
            'image_cover' => 'required',
            'description' => 'required',
            'teacher_id' => 'required|exists:users,id',
            'category_id' => 'required',
        ];

        if ($webinar->isWebinar()) {
            $rules['start_date'] = 'required|date';
            $rules['duration'] = 'required';
            $rules['capacity'] = 'required|integer';
        }

        $this->validate($request, $rules);

        if (!empty($data['teacher_id'])) {
            $teacher = User::find($data['teacher_id']);
            $creator = $webinar->creator;

            if (empty($teacher) or ($creator->isOrganization() and ($teacher->organ_id != $creator->id and $teacher->id != $creator->id))) {
                $toastData = [
                    'title' => trans('public.request_failed'),
                    'msg' => trans('admin/main.is_not_the_teacher_of_this_organization'),
                    'status' => 'error'
                ];
                return back()->with(['toast' => $toastData]);
            }
        }


        $data['status'] = $publish ? Webinar::$active : ($reject ? Webinar::$inactive : ($isDraft ? Webinar::$isDraft : Webinar::$pending));
        $data['updated_at'] = time();

        if ($webinar->type == 'webinar') {
            $data['start_date'] = strtotime($data['start_date']);
        }

        $data['private'] = (!empty($data['private']) and $data['private'] == 'on');
        $data['support'] = !empty($data['support']) ? true : false;
        $data['downloadable'] = !empty($data['downloadable']) ? true : false;
        $data['partner_instructor'] = !empty($data['partner_instructor']) ? true : false;

        if (empty($data['partner_instructor'])) {
            WebinarPartnerTeacher::where('webinar_id', $webinar->id)->delete();
            unset($data['partners']);
        }

        if ($data['category_id'] !== $webinar->category_id) {
            WebinarFilterOption::where('webinar_id', $webinar->id)->delete();
        }

        $data['subscribe'] = !empty($data['subscribe']) ? true : false;

        $filters = $request->get('filters', null);
        if (!empty($filters) and is_array($filters)) {
            WebinarFilterOption::where('webinar_id', $webinar->id)->delete();
            foreach ($filters as $filter) {
                WebinarFilterOption::create([
                    'webinar_id' => $webinar->id,
                    'filter_option_id' => $filter
                ]);
            }
        }

        if (!empty($request->get('tags'))) {
            $tags = explode(',', $request->get('tags'));
            Tag::where('webinar_id', $webinar->id)->delete();

            foreach ($tags as $tag) {
                Tag::create([
                    'webinar_id' => $webinar->id,
                    'title' => $tag,
                ]);
            }
        }

        if (!empty($request->get('partner_instructor')) and !empty($request->get('partners'))) {
            WebinarPartnerTeacher::where('webinar_id', $webinar->id)->delete();

            foreach ($request->get('partners') as $partnerId) {
                WebinarPartnerTeacher::create([
                    'webinar_id' => $webinar->id,
                    'teacher_id' => $partnerId,
                ]);
            }
        }
        unset($data['_token'], $data['current_step'], $data['draft'], $data['get_next'], $data['partners'], $data['tags'], $data['filters'], $data['ajax']);

        $webinar->slug = null; // regenerate slug in model

        $webinar->update($data);

        if ($publish) {
            sendNotification('course_approve', ['[c.title]' => $webinar->title], $webinar->teacher_id);
        } elseif ($reject) {
            sendNotification('course_reject', ['[c.title]' => $webinar->title], $webinar->teacher_id);
        }

        return redirect('/admin/webinars?type=' . $webinar->type);
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_webinars_delete');

        Webinar::find($id)->delete();

        return redirect('/admin/webinars');
    }

    public function search(Request $request)
    {
        $term = $request->get('term');
        $webinar = Webinar::select('id', 'title')
            ->where('title', 'like', "%$term%")
            ->get();

        return response()->json($webinar, 200);
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_webinars_export_excel');

        $query = Webinar::query();

        $query = $this->filterWebinar($query, $request)
            ->with(['teacher' => function ($qu) {
                $qu->select('id', 'full_name');
            }, 'sales']);

        $webinars = $query->get();

        $webinarExport = new WebinarsExport($webinars);

        return Excel::download($webinarExport, 'webinars.xlsx');
    }
}
