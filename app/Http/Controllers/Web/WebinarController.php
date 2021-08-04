<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\AdvertisingBanner;
use App\Models\Favorite;
use App\Models\File;
use App\Models\QuizzesResult;
use App\Models\Sale;
use App\Models\TextLesson;
use App\Models\CourseLearning;
use App\Models\WebinarReport;
use App\Models\Webinar;
use Illuminate\Http\Request;

class WebinarController extends Controller
{
    public function course($slug)
    {
        $user = null;

        if (auth()->check()) {
            $user = auth()->user();
        }

        $course = Webinar::where('slug', $slug)
            ->with([
                'quizzes' => function ($query) {
                    $query->with(['quizResults', 'quizQuestions']);
                },
                'tags',
                'prerequisites' => function ($query) {
                    $query->with(['prerequisiteWebinar' => function ($query) {
                        $query->with(['teacher' => function ($qu) {
                            $qu->select('id', 'full_name', 'avatar');
                        }]);
                    }]);
                    $query->orderBy('order', 'asc');
                },
                'faqs' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
                'files' => function ($query) use ($user) {
                    $query->orderBy('order', 'asc')
                        ->with([
                            'learningStatus' => function ($query) use ($user) {
                                $query->where('user_id', !empty($user) ? $user->id : null);
                            }
                        ]);
                },
                'textLessons' => function ($query) use ($user) {
                    $query->withCount(['attachments'])
                        ->orderBy('order', 'asc')
                        ->with([
                            'learningStatus' => function ($query) use ($user) {
                                $query->where('user_id', !empty($user) ? $user->id : null);
                            }
                        ]);
                },
                'sessions' => function ($query) use ($user) {
                    $query->orderBy('order', 'asc')
                        ->with([
                            'learningStatus' => function ($query) use ($user) {
                                $query->where('user_id', !empty($user) ? $user->id : null);
                            }
                        ]);
                },
                'tickets' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
                'filterOptions',
                'category',
                'teacher',
                'reviews' => function ($query) {
                    $query->where('status', 'active');
                    $query->with([
                        'comments',
                        'creator' => function ($qu) {
                            $qu->select('id', 'full_name', 'avatar');
                        }
                    ]);
                },
                'comments' => function ($query) {
                    $query->where('status', 'active');
                    $query->whereNull('reply_id');
                    $query->with([
                        'user' => function ($query) {
                            $query->select('id', 'full_name', 'role_name', 'role_id', 'avatar');
                        },
                        'replies' => function ($query) {
                            $query->where('status', 'active');
                            $query->with([
                                'user' => function ($query) {
                                    $query->select('id', 'full_name', 'role_name', 'role_id', 'avatar');
                                }
                            ]);
                        }
                    ]);
                    $query->orderBy('created_at', 'desc');
                },
            ])
            ->withCount([
                'sales' => function ($query) {
                    $query->whereNull('refund_at');
                }
            ])
            ->where('status', 'active')
            ->first();

        if (empty($course)) {
            return back();
        }

        $isPrivate = $course->private;
        if (!empty($user) and ($user->id == $course->creator_id or $user->organ_id == $course->creator_id or $user->isAdmin())) {
            $isPrivate = false;
        }

        if ($isPrivate) {
            return back();
        }

        $isFavorite = false;

        if (!empty($user)) {
            $isFavorite = Favorite::where('webinar_id', $course->id)
                ->where('user_id', $user->id)
                ->first();
        }

        $hasBought = $course->checkUserHasBought($user);

        $webinarContentCount = 0;
        if (!empty($course->sessions)) {
            $webinarContentCount += $course->sessions->count();
        }
        if (!empty($course->files)) {
            $webinarContentCount += $course->files->count();
        }
        if (!empty($course->textLessons)) {
            $webinarContentCount += $course->textLessons->count();
        }
        if (!empty($course->quizzes)) {
            $webinarContentCount += $course->quizzes->count();
        }

        $advertisingBanners = AdvertisingBanner::where('published', true)
            ->whereIn('position', ['course', 'course_sidebar'])
            ->get();

        $canDownloadCertificate = false;

        if ($user) {
            $quizzes = $course->quizzes;
            foreach ($quizzes as $quiz) {
                $canTryAgainQuiz = false;
                $userQuizDone = QuizzesResult::where('quiz_id', $quiz->id)
                    ->where('user_id', $user->id)
                    ->orderBy('id', 'desc')
                    ->get();

                if (count($userQuizDone)) {
                    $quiz->user_grade = $userQuizDone->first()->user_grade;
                    $quiz->result_status = $userQuizDone->first()->status;
                    $quiz->result_count = $userQuizDone->count();
                    $quiz->result = $userQuizDone->first();

                    if ($quiz->result_status == 'passed') {
                        $canDownloadCertificate = true;
                    }
                }

                if (!isset($quiz->attempt) or (count($userQuizDone) < $quiz->attempt and $quiz->result_status !== 'pass')) {
                    $canTryAgainQuiz = true;
                }

                $quiz->can_try = $canTryAgainQuiz;
                $quiz->can_download_certificate = $canDownloadCertificate;
            }
        }

        $data = [
            'pageTitle' => $course->title,
            'course' => $course,
            'isFavorite' => $isFavorite,
            'hasBought' => $hasBought,
            'user' => $user,
            'webinarContentCount' => $webinarContentCount,
            'advertisingBanners' => $advertisingBanners->where('position', 'course'),
            'advertisingBannersSidebar' => $advertisingBanners->where('position', 'course_sidebar'),
            'activeSpecialOffer' => $course->activeSpecialOffer(),
        ];

        return view('web.default.course.index', $data);
    }

    public function downloadFile($slug, $file_id)
    {
        $webinar = Webinar::where('slug', $slug)
            ->where('private', false)
            ->where('status', 'active')
            ->first();

        if (!empty($webinar)) {
            $file = File::where('webinar_id', $webinar->id)
                ->where('id', $file_id)
                ->first();

            if (!empty($file) and $file->downloadable) {
                $canAccess = true;

                if ($file->accessibility == 'paid') {
                    $canAccess = $webinar->checkUserHasBought();
                }

                if ($canAccess) {
                    $filePath = public_path($file->file);

                    $fileName = str_replace(' ', '-', $file->title);
                    $fileName = str_replace('.', '-', $fileName);
                    $fileName .= '.' . $file->file_type;

                    $headers = array(
                        'Content-Type: application/' . $file->file_type,
                    );

                    return response()->download($filePath, $fileName, $headers);
                } else {
                    $toastData = [
                        'title' => trans('public.not_access_toast_lang'),
                        'msg' => trans('public.not_access_toast_msg_lang'),
                        'status' => 'error'
                    ];
                    return back()->with(['toast' => $toastData]);
                }
            }
        }

        return back();
    }

    public function getFilePath(Request $request)
    {
        $this->validate($request, [
            'file_id' => 'required'
        ]);

        $file_id = $request->get('file_id');

        $file = File::where('id', $file_id)
            ->first();

        if (!empty($file)) {
            $webinar = Webinar::where('id', $file->webinar_id)
                ->where('status', 'active')
                ->first();

            if (!empty($webinar)) {
                $canAccess = true;

                if ($file->accessibility == 'paid') {
                    $canAccess = $webinar->checkUserHasBought();
                }

                if ($canAccess) {
                    $storageService = 'youtube';

                    if (strpos($file->file, 'vimeo')) {
                        $storageService = 'vimeo';
                    }

                    return response()->json([
                        'code' => 200,
                        'storage' => $file->storage,
                        'path' => $file->storage == 'local' ? url($file->file) : $file->file,
                        'storageService' => $storageService
                    ], 200);
                }
            }
        }

        return response()->json([], 422);
    }

    public function getLesson(Request $request, $slug, $lesson_id)
    {
        $user = null;

        if (auth()->check()) {
            $user = auth()->user();
        }

        $course = Webinar::where('slug', $slug)
            ->where('private', false)
            ->where('status', 'active')
            ->with(['teacher', 'textLessons' => function ($query) {
                $query->orderBy('order', 'asc');
            }])
            ->first();

        if (!empty($course)) {
            $textLesson = TextLesson::where('id', $lesson_id)
                ->where('webinar_id', $course->id)
                ->with([
                    'attachments' => function ($query) {
                        $query->with('file');
                    },
                    'learningStatus' => function ($query) use ($user) {
                        $query->where('user_id', !empty($user) ? $user->id : null);
                    }
                ])
                ->first();

            if (!empty($textLesson)) {
                $canAccess = $course->checkUserHasBought();

                if ($textLesson->accessibility == 'paid' and !$canAccess) {
                    return back();
                }

                $nextLesson = null;
                $previousLesson = null;
                if (!empty($course->textLessons) and count($course->textLessons)) {
                    $nextLesson = $course->textLessons->where('order', '>', $textLesson->order)->first();
                    $previousLesson = $course->textLessons->where('order', '<', $textLesson->order)->first();
                }

                $data = [
                    'pageTitle' => $textLesson->title,
                    'textLesson' => $textLesson,
                    'course' => $course,
                    'nextLesson' => $nextLesson,
                    'previousLesson' => $previousLesson,
                ];

                return view(getTemplate() . '.course.text_lesson', $data);
            }
        }

        abort(404);
    }

    public function free(Request $request, $slug)
    {
        $user = auth()->user();
        $course = Webinar::where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (!empty($course)) {
            $checkCourseForSale = checkCourseForSale($course, $user);

            if ($checkCourseForSale != 'ok') {
                return $checkCourseForSale;
            }

            if (!empty($course->price) and $course->price > 0) {
                $toastData = [
                    'title' => trans('cart.fail_purchase'),
                    'msg' => trans('cart.course_not_free'),
                    'status' => 'error'
                ];
                return back()->with(['toast' => $toastData]);
            }

            Sale::create([
                'buyer_id' => $user->id,
                'seller_id' => $course->creator_id,
                'webinar_id' => $course->id,
                'type' => Sale::$webinar,
                'payment_method' => Sale::$credit,
                'amount' => 0,
                'created_at' => time(),
            ]);

            $toastData = [
                'title' => '',
                'msg' => trans('cart.success_pay_msg_for_free_course'),
                'status' => 'success'
            ];
            return back()->with(['toast' => $toastData]);
        }

        abort(404);
    }

    public function reportWebinar(Request $request, $id)
    {
        if (auth()->check()) {
            $user_id = auth()->id();

            $this->validate($request, [
                'reason' => 'required|string',
                'message' => 'required|string',
            ]);

            $data = $request->all();

            $webinar = Webinar::select('id', 'status')
                ->where('id', $id)
                ->where('status', 'active')
                ->first();

            if (!empty($webinar)) {
                WebinarReport::create([
                    'user_id' => $user_id,
                    'webinar_id' => $webinar->id,
                    'reason' => $data['reason'],
                    'message' => $data['message'],
                    'created_at' => time()
                ]);

                return response()->json([
                    'code' => 200
                ], 200);
            }
        }

        return response()->json([
            'code' => 401
        ], 200);
    }

    public function learningStatus(Request $request, $id)
    {
        if (auth()->check()) {
            $user = auth()->user();

            $course = Webinar::where('id', $id)->first();

            if (!empty($course) and $course->checkUserHasBought($user)) {
                $data = $request->all();

                $item = $data['item'];
                $item_id = $data['item_id'];
                $status = $data['status'];

                CourseLearning::where('user_id', $user->id)
                    ->where($item, $item_id)
                    ->delete();

                if ($status and $status == "true") {
                    CourseLearning::create([
                        'user_id' => $user->id,
                        $item => $item_id,
                        'created_at' => time()
                    ]);
                }

                return response()->json([], 200);
            }
        }

        return response()->json([], 403);
    }
}
