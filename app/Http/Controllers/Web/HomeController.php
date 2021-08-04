<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AdvertisingBanner;
use App\Models\Blog;
use App\Models\FeatureWebinar;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SpecialOffer;
use App\Models\Subscribe;
use App\Models\Ticket;
use App\Models\TrendCategory;
use App\Models\Webinar;
use App\Models\Testimonial;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $homeSectionsSettings = getHomeSectionsSettings();

        $featureWebinars = FeatureWebinar::whereIn('page', ['home', 'home_categories'])
            ->where('status', 'publish')
            ->with([
                'webinar' => function ($query) {
                    $query->with([
                        'teacher' => function ($qu) {
                            $qu->select('id', 'full_name', 'avatar');
                        },
                        'reviews' => function ($query) {
                            $query->where('status', 'active');
                        },
                        'tickets',
                        'feature'
                    ]);
                }
            ])
            ->orderBy('updated_at', 'desc')
            ->get();
        //$selectedWebinarIds = $featureWebinars->pluck('id')->toArray();

        if (!empty($homeSectionsSettings) and !empty($homeSectionsSettings['latest_classes']) and $homeSectionsSettings['latest_classes']) {
            $latestWebinars = Webinar::where('status', Webinar::$active)
                ->where('private', false)
                ->orderBy('updated_at', 'desc')
                ->with([
                    'teacher' => function ($qu) {
                        $qu->select('id', 'full_name', 'avatar');
                    },
                    'reviews' => function ($query) {
                        $query->where('status', 'active');
                    },
                    'tickets',
                    'feature'
                ])
                ->limit(6)
                ->get();

            //$selectedWebinarIds = array_merge($selectedWebinarIds, $latestWebinars->pluck('id')->toArray());
        }

        if (!empty($homeSectionsSettings) and !empty($homeSectionsSettings['best_sellers']) and $homeSectionsSettings['best_sellers']) {
            $bestSaleWebinarsIds = Sale::whereNotNull('webinar_id')
                ->select(DB::raw('COUNT(id) as cnt,webinar_id'))
                ->groupBy('webinar_id')
                ->orderBy('cnt', 'DESC')
                ->limit(6)
                ->pluck('webinar_id')
                ->toArray();

            $bestSaleWebinars = Webinar::whereIn('id', $bestSaleWebinarsIds)
                ->where('status', Webinar::$active)
                ->where('private', false)
                ->with([
                    'teacher' => function ($qu) {
                        $qu->select('id', 'full_name', 'avatar');
                    },
                    'reviews' => function ($query) {
                        $query->where('status', 'active');
                    },
                    'sales',
                    'tickets',
                    'feature'
                ])
                ->get();

            //$selectedWebinarIds = array_merge($selectedWebinarIds, $bestSaleWebinars->pluck('id')->toArray());
        }

        if (!empty($homeSectionsSettings) and !empty($homeSectionsSettings['best_rates']) and $homeSectionsSettings['best_rates']) {
            $bestRateWebinars = Webinar::join('webinar_reviews', 'webinars.id', '=', 'webinar_reviews.webinar_id')
                ->select('webinars.*', 'webinar_reviews.rates', 'webinar_reviews.status', DB::raw('avg(rates) as avg_rates'))
                ->where('webinars.status', 'active')
                ->where('webinars.private', false)
                ->where('webinar_reviews.status', 'active')
                ->groupBy('teacher_id')
                ->orderBy('avg_rates', 'desc')
                ->with([
                    'teacher' => function ($qu) {
                        $qu->select('id', 'full_name', 'avatar');
                    }
                ])
                ->limit(6)
                ->get();
        }

        // hasDiscountWebinars
        if (!empty($homeSectionsSettings) and !empty($homeSectionsSettings['discount_classes']) and $homeSectionsSettings['discount_classes']) {
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

            $hasDiscountWebinars = Webinar::whereIn('id', array_unique($webinarIdsHasDiscount))
                ->where('status', Webinar::$active)
                ->where('private', false)
                ->with([
                    'teacher' => function ($qu) {
                        $qu->select('id', 'full_name', 'avatar');
                    },
                    'reviews' => function ($query) {
                        $query->where('status', 'active');
                    },
                    'sales',
                    'tickets',
                    'feature'
                ])
                ->limit(6)
                ->get();
        }
        // .\ hasDiscountWebinars

        if (!empty($homeSectionsSettings) and !empty($homeSectionsSettings['free_classes']) and $homeSectionsSettings['free_classes']) {
            $freeWebinars = Webinar::where('status', Webinar::$active)
                ->where('private', false)
                ->where(function ($query) {
                    $query->whereNull('price')
                        ->orWhere('price', '0');
                })
                ->orderBy('updated_at', 'desc')
                ->with([
                    'teacher' => function ($qu) {
                        $qu->select('id', 'full_name', 'avatar');
                    },
                    'reviews' => function ($query) {
                        $query->where('status', 'active');
                    },
                    'tickets',
                    'feature'
                ])
                ->limit(6)
                ->get();
        }

        if (!empty($homeSectionsSettings) and !empty($homeSectionsSettings['trend_categories']) and $homeSectionsSettings['trend_categories']) {
            $trendCategories = TrendCategory::with(['category' => function ($query) {
                $query->withCount('webinars');
            }])->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        }

        if (!empty($homeSectionsSettings) and !empty($homeSectionsSettings['blog']) and $homeSectionsSettings['blog']) {
            $blog = Blog::where('status', 'publish')
                ->with(['category', 'author' => function ($query) {
                    $query->select('id', 'full_name');
                }])->orderBy('updated_at', 'desc')
                ->withCount('comments')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        }

        if (!empty($homeSectionsSettings) and !empty($homeSectionsSettings['instructors']) and $homeSectionsSettings['instructors']) {
            $instructors = User::where('role_name', Role::$teacher)
                ->select('id', 'full_name', 'avatar', 'bio')
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->where('ban', false)
                        ->orWhere(function ($query) {
                            $query->whereNotNull('ban_end_at')
                                ->where('ban_end_at', '<', time());
                        });
                })
                ->limit(8)
                ->get();
        }

        if (!empty($homeSectionsSettings) and !empty($homeSectionsSettings['organizations']) and $homeSectionsSettings['organizations']) {
            $organizations = User::where('role_name', Role::$organization)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->where('ban', false)
                        ->orWhere(function ($query) {
                            $query->whereNotNull('ban_end_at')
                                ->where('ban_end_at', '<', time());
                        });
                })
                ->withCount('webinars')
                ->orderBy('webinars_count', 'desc')
                ->limit(6)
                ->get();
        }

        if (!empty($homeSectionsSettings) and !empty($homeSectionsSettings['testimonials']) and $homeSectionsSettings['testimonials']) {
            $testimonials = Testimonial::where('status', 'active')->get();
        }

        if (!empty($homeSectionsSettings) and !empty($homeSectionsSettings['subscribes']) and $homeSectionsSettings['subscribes']) {
            $subscribes = Subscribe::all();
        }

        $advertisingBanners = AdvertisingBanner::where('published', true)
            ->whereIn('position', ['home1', 'home2'])
            ->get();

        $skillfulTeachersCount = User::where('role_name', Role::$teacher)
            ->where(function ($query) {
                $query->where('ban', false)
                    ->orWhere(function ($query) {
                        $query->whereNotNull('ban_end_at')
                            ->where('ban_end_at', '<', time());
                    });
            })
            ->where('status', 'active')
            ->count();

        $studentsCount = User::where('role_name', Role::$user)
            ->where(function ($query) {
                $query->where('ban', false)
                    ->orWhere(function ($query) {
                        $query->whereNotNull('ban_end_at')
                            ->where('ban_end_at', '<', time());
                    });
            })
            ->where('status', 'active')
            ->count();

        $liveClassCount = Webinar::where('type', 'webinar')
            ->where('status', 'active')
            ->count();

        $offlineCourseCount = Webinar::where('status', 'active')
            ->whereIn('type', ['course', 'text_lesson'])
            ->count();

        $siteGeneralSettings = getGeneralSettings();
        $heroSection = (!empty($siteGeneralSettings['hero_section2']) and $siteGeneralSettings['hero_section2'] == "1") ? "2" : "1";
        $heroSectionData = getHomeHeroSettings($heroSection);

        if (!empty($homeSectionsSettings) and !empty($homeSectionsSettings['video_or_image_section']) and $homeSectionsSettings['video_or_image_section']) {
            $boxVideoOrImage = getHomeVideoOrImageBoxSettings();
        }

        $seoSettings = getSeoMetas('home');
        $pageTitle = !empty($seoSettings['title']) ? $seoSettings['title'] : trans('home.home_title');
        $pageDescription = !empty($seoSettings['description']) ? $seoSettings['description'] : trans('home.home_title');
        $pageRobot = getPageRobot('home');

        $data = [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageRobot' => $pageRobot,
            'heroSection' => $heroSection,
            'heroSectionData' => $heroSectionData,
            'featureWebinars' => $featureWebinars,
            'latestWebinars' => $latestWebinars ?? [],
            'bestSaleWebinars' => $bestSaleWebinars ?? [],
            'hasDiscountWebinars' => $hasDiscountWebinars ?? [],
            'bestRateWebinars' => $bestRateWebinars ?? [],
            'freeWebinars' => $freeWebinars ?? [],
            'trendCategories' => $trendCategories ?? [],
            'instructors' => $instructors ?? [],
            'testimonials' => $testimonials ?? [],
            'subscribes' => $subscribes ?? [],
            'blog' => $blog ?? [],
            'organizations' => $organizations ?? [],
            'advertisingBanners1' => $advertisingBanners->where('position', 'home1'),
            'advertisingBanners2' => $advertisingBanners->where('position', 'home2'),
            'skillfulTeachersCount' => $skillfulTeachersCount,
            'studentsCount' => $studentsCount,
            'liveClassCount' => $liveClassCount,
            'offlineCourseCount' => $offlineCourseCount,
            'boxVideoOrImage' => $boxVideoOrImage ?? null,
        ];

        return view(getTemplate() . '.pages.home', $data);
    }
}
