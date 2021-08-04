<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FeatureWebinar;
use App\Models\Sale;
use App\Models\Ticket;
use App\Models\Webinar;
use App\Models\WebinarFilterOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    public function index(Request $request, $item)
    {
        if (!empty($item)) {

            $category = Category::where('title', str_replace('-', ' ', $item))
                ->withCount('webinars')
                ->with(['filters' => function ($query) {
                    $query->with('options');
                }])
                ->first();

            if (!empty($category)) {
                $featureWebinars = FeatureWebinar::whereIn('page', ['categories', 'home_categories'])
                    ->where('status', 'publish')
                    ->whereHas('webinar', function ($q) use ($category) {
                        $q->whereHas('category', function ($q) use ($category) {
                            $q->where('id', $category->id);
                        });
                    })
                    ->with(['webinar' => function ($query) {
                        $query->with(['teacher' => function ($qu) {
                            $qu->select('id', 'full_name', 'avatar');
                        }, 'reviews', 'tickets', 'feature']);
                    }])
                    ->orderBy('updated_at', 'desc')
                    ->get();


                $webinarsQuery = Webinar::where('status', 'active')
                    ->where('private', false)
                    ->where('category_id', $category->id);

                $classesController = new ClassesController();
                $webinars = $classesController->handleFilters($request, $webinarsQuery)
                    ->orderBy('created_at', 'desc')
                    ->orderBy('updated_at', 'desc')
                    ->with(['tickets', 'feature'])
                    ->paginate(6);

                $seoSettings = getSeoMetas('categories');
                $pageTitle = !empty($seoSettings['title']) ? $seoSettings['title'] : trans('site.categories_page_title');
                $pageDescription = !empty($seoSettings['description']) ? $seoSettings['description'] : trans('site.categories_page_title');
                $pageRobot = getPageRobot('categories');

                $data = [
                    'pageTitle' => $pageTitle,
                    'pageDescription' => $pageDescription,
                    'pageRobot' => $pageRobot,
                    'category' => $category,
                    'webinars' => $webinars,
                    'featureWebinars' => $featureWebinars
                ];

                return view(getTemplate() . '.pages.categories', $data);
            }
        }

        abort(404);
    }
}
