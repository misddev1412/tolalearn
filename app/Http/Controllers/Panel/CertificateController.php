<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Quiz;
use App\Models\QuizzesResult;
use App\Models\Sale;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class CertificateController extends Controller
{

    public function lists(Request $request)
    {
        $user = auth()->user();

        if (!$user->isUser()) {
            $query = Quiz::where('creator_id', $user->id)
                ->where('status', Quiz::ACTIVE);

            $activeQuizzes = $query->count();

            $quizzesIds = $query->pluck('id')->toArray();

            $achievementsCount = Certificate::whereIn('quiz_id', $quizzesIds)->count();

            $quizResultQuery = QuizzesResult::whereIn('quiz_id', $quizzesIds);
            $failedResults = deepClone($quizResultQuery)->where('status', QuizzesResult::$failed)->count();
            $avgGrade = deepClone($quizResultQuery)->where('status', QuizzesResult::$passed)->avg('user_grade');

            $userAllQuizzes = deepClone($query)->select('id', 'title', 'webinar_id')->get();

            $query = $this->quizFilters(deepClone($query), $request);

            $quizzes = $query->with([
                'webinar',
                'certificates',
                'quizResults' => function ($query) {
                    $query->orderBy('id', 'desc');
                },
            ])->paginate(10);

            foreach ($quizzes as $quiz) {
                $quizResults = $quiz->quizResults;

                $quiz->avg_grade = $quizResults->where('status', QuizzesResult::$passed)->avg('user_grade');
            }

            $userWebinars = Webinar::select('id', 'title')
                ->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id)
                        ->orWhere('teacher_id', $user->id);
                })
                ->where('status', 'active')
                ->get();

            $data = [
                'pageTitle' => trans('quiz.certificates_lists'),
                'quizzes' => $quizzes,
                'activeQuizzes' => $activeQuizzes,
                'achievementsCount' => $achievementsCount,
                'avgGrade' => round($avgGrade, 2),
                'failedResults' => $failedResults,
                'userWebinars' => $userWebinars,
                'userAllQuizzes' => $userAllQuizzes,
            ];

            return view('web.default.panel.certificates.list', $data);
        }

        abort(404);
    }

    public function achievements(Request $request)
    {
        $user = auth()->user();

        $results = QuizzesResult::where('user_id', $user->id);

        $failedQuizzes = deepClone($results)->where('status', QuizzesResult::$failed)->count();
        $avgGrades = deepClone($results)->where('status', QuizzesResult::$passed)->avg('user_grade');

        if (!empty($request->get('grade'))) {
            $results->where('user_grade', $request->get('grade'));
        }

        $quizzesIds = $results->where('status', QuizzesResult::$passed)
            ->pluck('quiz_id')
            ->toArray();
        $quizzesIds = array_unique($quizzesIds);

        $query = Quiz::whereIn('id', $quizzesIds)
            ->where('status', Quiz::ACTIVE);

        $certificatesCount = deepClone($query)->count();

        $userAllQuizzes = deepClone($query)->select('id', 'title', 'webinar_id')->get();

        $query = $this->quizFilters(deepClone($query), $request);

        $quizzes = $query->with([
            'webinar',
            'quizResults' => function ($query) {
                $query->orderBy('id', 'desc');
            },
        ])->paginate(10);


        $canDownloadCertificate = false;
        foreach ($quizzes as $quiz) {
            $userQuizDone = $quiz->quizResults;

            if (count($userQuizDone)) {
                $quiz->result = $userQuizDone->first();

                if ($quiz->result->status == 'passed') {
                    $canDownloadCertificate = true;
                }
            }

            $quiz->can_download_certificate = $canDownloadCertificate;
        }

        $webinarsIds = $user->getPurchasedCoursesIds();
        $userWebinars = Webinar::select('id', 'title')
            ->whereIn('id', $webinarsIds)
            ->where('status', 'active')
            ->get();

        $data = [
            'pageTitle' => trans('quiz.my_achievements_lists'),
            'quizzes' => $quizzes,
            'failedQuizzes' => $failedQuizzes,
            'avgGrades' => round($avgGrades, 2),
            'certificatesCount' => $certificatesCount,
            'userWebinars' => $userWebinars,
            'userAllQuizzes' => $userAllQuizzes,
        ];

        return view(getTemplate() . '.panel.certificates.achievements', $data);
    }

    private function quizFilters($query, $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $webinar_id = $request->get('webinar_id');
        $quiz_id = $request->get('quiz_id');
        $grade = $request->get('grade');


        if (!empty($from) and !empty($to)) {
            $from = strtotime($from);
            $to = strtotime($to);

            $query->whereBetween('created_at', [$from, $to]);
        } else {
            if (!empty($from)) {
                $from = strtotime($from);
                $query->where('created_at', '>=', $from);
            }

            if (!empty($to)) {
                $to = strtotime($to);

                $query->where('created_at', '<', $to);
            }
        }

        if (!empty($webinar_id)) {
            $query->where('webinar_id', $webinar_id);
        }

        if (!empty($quiz_id)) {
            $query->where('id', $quiz_id);
        }

        return $query;
    }

    public function downloadCertificate($quizResultId)
    {
        $user = auth()->user();

        $makeCertificate = $this->makeCertificate($quizResultId);

        $file_path = $this->saveCertificate($user, $makeCertificate);

        if (!empty($file_path)) {
            if (file_exists(public_path($file_path))) {
                return response()->download(public_path($file_path));
            }
        }

        abort(404);
    }

    public function showCertificate($quizResultId)
    {
        $user = auth()->user();
        $makeCertificate = $this->makeCertificate($quizResultId);

        $this->saveCertificate($user, $makeCertificate);

        $img = $makeCertificate['img'];

        return $img->response('png');
    }

    private function saveCertificate($user, $makeCertificate)
    {
        $quiz = $makeCertificate['quiz'];
        $quizResult = $makeCertificate['quizResult'];
        $img = $makeCertificate['img'];

        if (!empty($img)) {
            $path = public_path("store/$user->id/certificates");

            if (!is_dir($path)) {
                File::makeDirectory($path, 0777, true);
            }

            $file_path = $path . '/' . $quiz->webinar->title . '(' . $quiz->name . ').jpg';
            if (is_file($file_path)) {
                $file_path = $path . '/' . $quiz->webinar->title . '(' . $quiz->name . '-' . $quizResult->user_grade . ').jpg';
            }

            $img->save($file_path);

            $file_path = '/' . explode('/public/', $file_path)[1];

            $certificate = Certificate::where('quiz_id', $quiz->id)
                ->where('student_id', $user->id)
                ->where('quiz_result_id', $quizResult->id)
                ->first();

            $data = [
                'quiz_id' => $quiz->id,
                'student_id' => $user->id,
                'quiz_result_id' => $quizResult->id,
                'user_grade' => $quizResult->user_grade,
                'file' => $file_path,
                'created_at' => time()
            ];

            if (!empty($certificate)) {
                $certificate->update($data);
            } else {
                Certificate::create($data);

                $notifyOptions = [
                    '[c.title]' => $quiz->webinar_title,
                ];
                sendNotification('new_certificate', $notifyOptions, $user->id);
            }

            return $file_path;
        }

        return null;
    }

    private function makeCertificate($quizResultId)
    {
        $user = auth()->user();

        $quizResult = QuizzesResult::where('id', $quizResultId)
            ->where('user_id', $user->id)
            ->where('status', QuizzesResult::$passed)
            ->with(['quiz' => function ($query) {
                $query->with(['webinar']);
            }])
            ->first();

        if (!empty($quizResult) and !empty($quizResult->quiz)) {
            $certificateTemplate = CertificateTemplate::where('status', 'publish')->first();

            if (!empty($certificateTemplate)) {
                $quiz = $quizResult->quiz;

                $certificate = Certificate::where('quiz_id', $quiz->id)
                    ->where('student_id', $user->id)
                    ->where('quiz_result_id', $quizResult->id)
                    ->first();

                if (empty($certificate)) {
                    $certificate = Certificate::create([
                        'quiz_id' => $quiz->id,
                        'student_id' => $user->id,
                        'quiz_result_id' => $quizResult->id,
                        'created_at' => time()
                    ]);
                }

                $img = Image::make(public_path($certificateTemplate->image));
                $body = $certificateTemplate->body;
                $body = str_replace('[student]', $user->full_name, $body);
                $body = str_replace('[course]', $quiz->webinar->title, $body);
                $body = str_replace('[grade]', $quizResult->user_grade, $body);
                $body = str_replace('[certificate_id]', $certificate->id, $body);

                $img->text($body, $certificateTemplate->position_x, $certificateTemplate->position_y, function ($font) use ($certificateTemplate) {
                    $font->file(public_path('assets/default/fonts/Montserrat-Medium.ttf'));
                    $font->size($certificateTemplate->font_size);
                    $font->color($certificateTemplate->text_color);
                });

                return [
                    'img' => $img,
                    'quizResult' => $quizResult,
                    'quiz' => $quiz,
                ];
            }
        }

        abort(404);
    }
}
