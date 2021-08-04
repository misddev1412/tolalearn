<?php

namespace App\Http\Middleware;

use App\Models\Page;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use League\Flysystem\Config;

class Share
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

        if (auth()->check()) {
            $user = auth()->user();
            view()->share('authUser', $user);

            if (!auth()->user()->isAdmin()) {
                $totalCartsPrice = 0;
                $carts = $user->carts()
                    ->with([
                        'webinar'
                    ])
                    ->get();

                foreach ($carts as $cart) {
                    if (!empty($cart->ticket_id) or !empty($cart->special_offer_id)) {
                        $totalCartsPrice += $cart->webinar->price - $cart->webinar->getDiscount($cart->ticket);
                    } else if (!empty($cart->webinar_id)) {
                        $totalCartsPrice += $cart->webinar->price;
                    } else if (!empty($cart->reserve_meeting_id)) {
                        $totalCartsPrice += $cart->reserveMeeting->paid_amount;
                    }
                }

                $unReadNotifications = $user->getUnReadNotifications();

                view()->share('userCarts', $carts);
                view()->share('totalCartsPrice', $totalCartsPrice);
                view()->share('unReadNotifications', $unReadNotifications);
            }
        }

        $generalSettings = getGeneralSettings();
        view()->share('generalSettings', $generalSettings);
        $currency = currencySign();
        view()->share('currency', $currency);


        // locale config
        if (!Session::has('locale')) {
            Session::put('locale', mb_strtolower($generalSettings['site_language']));
        }
        App::setLocale(session('locale'));

        view()->share('categories', \App\Models\Category::getCategories());
        view()->share('navbarPages', getNavbarLinks());

        return $next($request);
    }
}
