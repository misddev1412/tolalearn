<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FeatureWebinar;
use App\Models\Ticket;
use App\Models\Webinar;
use App\Models\WebinarFilterOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassesController extends Controller
{
    public function index(Request $request)
    {
        $webinarsQuery = Webinar::where('status', 'active')
            ->where('private', false);

        $webinarsQuery = $this->handleFilters($request, $webinarsQuery);

        $coursesCount = $webinarsQuery->count();

        $webinars = $webinarsQuery->orderBy('webinars.created_at', 'desc')
            ->orderBy('webinars.updated_at', 'desc')
            ->with(['tickets', 'feature'])
            ->paginate(6);

        $seoSettings = getSeoMetas('classes');
        $pageTitle = $seoSettings['title'] ?? '';
        $pageDescription = $seoSettings['description'] ?? '';
        $pageRobot = getPageRobot('classes');

        $data = [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageRobot' => $pageRobot,
            'webinars' => $webinars,
            'coursesCount' => $coursesCount
        ];

        return view(getTemplate() . '.pages.classes', $data);
    }

    public function handleFilters($request, $query)
    {
        $upcoming = $request->get('upcoming', null);
        $isFree = $request->get('free', null);
        $withDiscount = $request->get('discount', null);
        $isDownloadable = $request->get('downloadable', null);
        $sort = $request->get('sort', null);
        $filterOptions = $request->get('filter_option', []);
        $typeOptions = $request->get('type', []);
        $moreOptions = $request->get('moreOptions', []);

        if (!empty($upcoming) and $upcoming == 'on') {
            $query->whereNotNull('start_date')
                ->where('start_date', '>=', time());
        }

        if (!empty($isFree) and $isFree == 'on') {
            $query->where(function ($qu) {
                $qu->whereNull('price')
                    ->orWhere('price', '0');
            });
        }

        if (!empty($isDownloadable) and $isDownloadable == 'on') {
            $query->where('downloadable', 1);
        }

        if (!empty($withDiscount) and $withDiscount == 'on') {
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

            $webinarIdsHasDiscount = array_unique($webinarIdsHasDiscount);

            $query->whereIn('webinars.id', $webinarIdsHasDiscount);
        }

        if (!empty($sort)) {
            if ($sort == 'expensive') {
                $query->orderBy('price', 'desc');
            }

            if ($sort == 'inexpensive') {
                $query->orderBy('price', 'asc');
            }

            if ($sort == 'bestsellers') {
                $query->whereHas('sales')
                    ->with('sales')
                    ->get()
                    ->sortBy(function ($qu) {
                        return $qu->sales->count();
                    });
            }

            if ($sort == 'best_rates') {
                $query->whereHas('reviews', function ($query) {
                    $query->where('status', 'active');
                })->with('reviews')
                    ->get()
                    ->sortBy(function ($qu) {
                        return $qu->reviews->avg('rates');
                    });
            }
        }

        if (!empty($filterOptions) and is_array($filterOptions)) {
            $webinarIdsFilterOptions = WebinarFilterOption::whereIn('filter_option_id', $filterOptions)
                ->pluck('webinar_id')
                ->toArray();

            $query->whereIn('webinars.id', $webinarIdsFilterOptions);
        }

        if (!empty($typeOptions) and is_array($typeOptions)) {
            $query->whereIn('type', $typeOptions);
        }

        if (!empty($moreOptions) and is_array($moreOptions)) {
            if (in_array('subscribe', $moreOptions)) {
                $query->where('subscribe', 1);
            }

            if (in_array('certificate_included', $moreOptions)) {
                $query->whereHas('quizzes', function ($query) {
                    $query->where('certificate', 1)
                        ->where('status', 'active');
                });
            }

            if (in_array('with_quiz', $moreOptions)) {
                $query->whereHas('quizzes', function ($query) {
                    $query->where('status', 'active');
                });
            }

            if (in_array('featured', $moreOptions)) {
                $query->whereHas('feature', function ($query) {
                    $query->whereIn('page', ['home_categories', 'categories'])
                        ->where('status', 'publish');
                });
            }
        }

        return $query;
    }
}
