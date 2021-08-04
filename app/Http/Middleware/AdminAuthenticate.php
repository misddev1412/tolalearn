<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Admin\SidebarController;
use App\User;
use Closure;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() and auth()->user()->isAdmin()) {

            if (auth()->user()->hasPermission('admin_notifications_list')) {
                $adminUser = User::find(1);

                \Session::forget('impersonated');

                $unreadNotifications = $adminUser->getUnReadNotifications();

                view()->share('unreadNotifications', $unreadNotifications);
            }

            $generalSettings = getGeneralSettings();
            view()->share('generalSettings', $generalSettings);

            $currency = currencySign();
            view()->share('currency', $currency);

            $user = auth()->user();
            view()->share('authUser', $user);

            $sidebarController = new SidebarController();

            $sidebarBeeps = [];
            $sidebarBeeps['courses'] = $sidebarController->getCoursesBeep();
            $sidebarBeeps['webinars'] = $sidebarController->getWebinarsBeep();
            $sidebarBeeps['textLessons'] = $sidebarController->getTextLessonsBeep();
            $sidebarBeeps['reviews'] = $sidebarController->getReviewsBeep();
            $sidebarBeeps['classesComments'] = $sidebarController->getClassesCommentsBeep();
            $sidebarBeeps['blogComments'] = $sidebarController->getBlogCommentsBeep();
            $sidebarBeeps['payoutRequest'] = $sidebarController->getPayoutRequestBeep();
            $sidebarBeeps['offlinePayments'] = $sidebarController->getOfflinePaymentsBeep();

            view()->share('sidebarBeeps', $sidebarBeeps);

            return $next($request);
        }

        return redirect('/admin/login');
    }
}
