<div class="dropdown">
    <button type="button" {{ (empty($userCarts) or count($userCarts) < 1) ? 'disabled' : '' }} class="btn btn-transparent dropdown-toggle" id="navbarShopingCart" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
        <i data-feather="shopping-cart" width="20" height="20" class="mr-10"></i>

        @if(!empty($userCarts) and count($userCarts))
            <span class="badge badge-circle-primary d-flex align-items-center justify-content-center">{{ count($userCarts) }}</span>
        @endif
    </button>

    <div class="dropdown-menu" aria-labelledby="navbarShopingCart">
        <div class="d-md-none border-bottom mb-20 pb-10 text-right">
            <i class="close-dropdown" data-feather="x" width="32" height="32" class="mr-10"></i>
        </div>
        <div class="h-100">
            <div class="navbar-shopping-cart h-100" data-simplebar>
                @if(!empty($userCarts) and !$userCarts->isEmpty())
                    <div class="mb-auto">
                        @foreach($userCarts as $cart)
                            <div class="navbar-cart-box d-flex align-items-center">
                                @php
                                    $imgPath = '';

                                    if (!empty($cart->webinar_id)) {
                                        $imgPath = $cart->webinar->getImage();
                                    } elseif (!empty($cart->reserve_meeting_id)) {
                                        $imgPath = $cart->reserveMeeting->meeting->creator->getAvatar();
                                    }
                                @endphp

                                @if(!empty($cart->webinar_id))
                                    <a href="{{ $cart->webinar->getUrl() }}" target="_blank" class="navbar-cart-img">
                                        <img src="{{ $imgPath }}" alt="product title" class="img-cover"/>
                                    </a>
                                    <div class="navbar-cart-info">
                                        <a href="{{ $cart->webinar->getUrl() }}" target="_blank">
                                            <h4>{{ $cart->webinar->title }}</h4>
                                        </a>
                                        <div class="price mt-10">
                                            @if($cart->webinar->getDiscount($cart->ticket) > 0)
                                                <span class="text-primary font-weight-bold">${{ $cart->webinar->price - $cart->webinar->getDiscount($cart->ticket) }}</span>
                                                <span class="off ml-15">${{ $cart->webinar->price }}</span>
                                            @else
                                                <span class="text-primary font-weight-bold">{{ $currency }}{{ $cart->webinar->price }}</span>
                                            @endif
                                        </div>
                                    </div>

                                @else
                                    <div class="navbar-cart-img">
                                        <img src="{{ $imgPath }}" alt="product title" class="img-cover"/>
                                    </div>

                                    <div class="navbar-cart-info">
                                        @if(!empty($cart->reserve_meeting_id))
                                            <h4>{{ trans('meeting.reservation_appointment') }}</h4>
                                        @endif

                                        <div class="price mt-10">
                                            <span class="text-primary font-weight-bold">
                                                {{ $currency }}
                                                @if(!empty($cart->reserve_meeting_id))
                                                    {{ $cart->reserveMeeting->meeting->amount }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="navbar-cart-actions">
                        <div class="navbar-cart-total mt-15 border-top d-flex align-items-center justify-content-between">
                            <strong class="total-text">{{ trans('cart.total') }}</strong>
                            <strong class="text-primary font-weight-bold">{{ $currency }}{{ !empty($totalCartsPrice) ? $totalCartsPrice : 0 }}</strong>
                        </div>

                        <a href="/cart/" class="btn btn-sm btn-primary btn-block mt-50 mt-md-15">{{ trans('cart.go_to_cart') }}</a>
                    </div>
                @else
                    <div class="d-flex align-items-center text-center py-50">
                        <i data-feather="shopping-cart" width="20" height="20" class="mr-10"></i>
                        <span class="">{{ trans('cart.your_cart_empty') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
