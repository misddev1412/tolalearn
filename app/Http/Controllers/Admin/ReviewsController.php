<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Webinar;
use App\Models\WebinarReview;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_reviews_lists');

        $query = WebinarReview::query();

        $totalReviews = deepClone($query)->count();
        $publishedReviews = deepClone($query)->where('status', 'active')->count();
        $ratesAverage = deepClone($query)->avg('rates');
        $classesWithoutReview = Webinar::where('status', Webinar::$active)->whereDoesntHave('reviews')->count();

        $query = $this->filters($query, $request);

        $reviews = $query->orderBy('created_at', 'desc')
            ->with([
                'webinar' => function ($query) {
                    $query->select('id', 'title', 'slug');
                },
                'creator' => function ($query) {
                    $query->select('id', 'full_name');
                },
            ])
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.reviews_list_title'),
            'totalReviews' => $totalReviews,
            'publishedReviews' => $publishedReviews,
            'ratesAverage' => round($ratesAverage, 2),
            'classesWithoutReview' => $classesWithoutReview,
            'reviews' => $reviews,
        ];

        $webinar_ids = $request->get('webinar_ids');
        if (!empty($webinar_ids)) {
            $data['webinars'] = Webinar::select('id', 'title')->whereIn('id', $webinar_ids)->get();
        }

        return view('admin.reviews.lists', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $search = $request->get('search', null);
        $webinar_ids = $request->get('webinar_ids');
        $status = $request->get('status', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($search)) {
            $query->where('description', 'like', "%$search%");
        }

        if (!empty($webinar_ids)) {
            $query->whereIn('webinar_id', $webinar_ids);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function toggleStatus($id)
    {
        $this->authorize('admin_reviews_status_toggle');

        $review = WebinarReview::findOrFail($id);

        $review->update([
            'status' => ($review->status == 'active') ? 'pending' : 'active',
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => 'Review status changed successful',
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }

    public function delete($id)
    {
        $this->authorize('admin_reviews_status_toggle');

        $review = WebinarReview::findOrFail($id);

        $review->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => 'Review deleted successful',
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }
}
