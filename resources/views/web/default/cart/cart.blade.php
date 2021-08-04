@extends(getTemplate().'.layouts.app')


@section('content')
    <section class="cart-banner position-relative text-center">
        <h1 class="font-30 text-white font-weight-bold">{{ trans('cart.shopping_cart') }}</h1>
        <span class="payment-hint font-20 text-white d-block"> {{'$' . $subTotal . ' ' . trans('cart.for_items',['count' => $carts->count()]) }}</span>
    </section>

    <div class="container">
        <section class="mt-45">
            <h2 class="section-title">{{ trans('cart.cart_items') }}</h2>

            <div class="rounded-sm shadow mt-20 py-25 px-10 px-md-30">
                @if($carts->count() > 0)
                    <div class="row d-none d-md-flex">
                        <div class="col-12 col-md-8 col-lg-6"><span
                                class="text-gray font-weight-500">{{ trans('cart.item') }}</span></div>
                        <div class="col-6 col-md-2 col-lg-3 text-center"><span
                                class="text-gray font-weight-500">{{ trans('public.price') }}</span></div>
                        <div class="col-6 col-md-2 col-lg-3 text-center"><span
                                class="text-gray font-weight-500">{{ trans('public.remove') }}</span></div>
                    </div>
                @endif
                @foreach($carts as $cart)
                    <div class="row mt-5 cart-row">
                        <div class="col-12 col-md-8 col-lg-6 mb-15 mb-md-0">
                            <div class="webinar-card webinar-list-cart d-flex">
                                <div class="image-box">
                                    @php
                                        $imgPath = '';

                                        if (!empty($cart->webinar_id)) {
                                            $imgPath = $cart->webinar->getImage();
                                        } elseif (!empty($cart->reserve_meeting_id)) {
                                            $imgPath = $cart->reserveMeeting->meeting->creator->getAvatar();
                                        }
                                    @endphp
                                    <img src="{{ $imgPath }}" class="img-cover" alt="">
                                </div>

                                <div class="webinar-card-body w-100 d-flex flex-column">
                                    <div class="d-flex align-items-center justify-content-between">
                                        @if(!empty($cart->webinar_id))
                                            <a href="{{ $cart->webinar->getUrl() }}" target="_blank">
                                                <h3 class="font-16 font-weight-bold text-dark-blue">{{ $cart->webinar->title }}</h3>
                                            </a>
                                        @elseif(!empty($cart->reserve_meeting_id))
                                            <h3 class="font-16 font-weight-bold text-dark-blue">{{ trans('meeting.reservation_appointment') }}</h3>
                                        @endif
                                    </div>

                                    @if(!empty($cart->reserve_meeting_id))
                                        <span class="text-gray font-12 mt-10">{{ $cart->reserveMeeting->day .' '. $cart->reserveMeeting->meetingTime->time }}</span>
                                    @endif

                                    <span class="text-gray font-14 mt-auto">
                                        {{ trans('public.by') }}

                                        @if(!empty($cart->webinar_id))
                                            <a href="{{ $cart->webinar->teacher->getProfileUrl() }}" target="_blank" class="text-gray text-decoration-underline">{{ $cart->webinar->teacher->full_name }}</a>
                                        @elseif(!empty($cart->reserve_meeting_id))
                                            <a href="{{  $cart->reserveMeeting->meeting->creator->getProfileUrl() }}" target="_blank" class="text-gray text-decoration-underline">{{  $cart->reserveMeeting->meeting->creator->full_name }}</a>
                                        @endif
                                    </span>

                                    @if(!empty($cart->webinar_id))
                                        @include(getTemplate() . '.includes.webinar.rate',['rate' => $cart->webinar->getRate()])
                                    @elseif(!empty($cart->reserve_meeting_id))
                                        @include(getTemplate() . '.includes.webinar.rate',['rate' =>  $cart->reserveMeeting->meeting->creator->rates()])
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-2 col-lg-3 d-flex flex-md-column align-items-center justify-content-center">
                            <span class="text-gray d-inline-block d-md-none">{{ trans('public.price') }} :</span>

                            @if(!empty($cart->webinar_id))
                                @if($cart->webinar->getDiscount($cart->ticket))
                                    <span class="text-gray text-decoration-line-through mx-10 mx-md-0">{{ $currency }}{{ $cart->webinar->price }}</span>
                                    <span class="font-20 text-primary mt-0 mt-md-5 font-weight-bold">{{ $currency }}{{ $cart->webinar->price - $cart->webinar->getDiscount($cart->ticket) }}</span>
                                @else
                                    <span class="font-20 text-primary mt-0 mt-md-5 font-weight-bold">{{ $currency }}{{ $cart->webinar->price }}</span>
                                @endif

                            @elseif(!empty($cart->reserve_meeting_id))
                                <span class="font-20 text-primary mt-0 mt-md-5 font-weight-bold">{{ $currency }}{{ $cart->reserveMeeting->paid_amount }}</span>
                            @endif
                        </div>

                        <div class="col-6 col-md-2 col-lg-3 d-flex flex-md-column align-items-center justify-content-center">
                            <span class="text-gray d-inline-block d-md-none mr-10 mr-md-0">{{ trans('public.remove') }} :</span>

                            <a href="/cart/{{ $cart->id }}/delete" class="delete-action btn-cart-list-delete d-flex align-items-center justify-content-center">
                                <i data-feather="x" width="20" height="20" class=""></i>
                            </a>
                        </div>
                    </div>
                @endforeach

                <button type="button" onclick="window.history.back()" class="btn btn-sm btn-primary mt-25">{{ trans('cart.continue_shopping') }}</button>
            </div>
        </section>

        <div class="row mt-30">
            <div class="col-12 col-lg-6">
                <section class="mt-45">
                    <h3 class="section-title">{{ trans('cart.coupon_code') }}</h3>
                    <div class="rounded-sm shadow mt-20 py-25 px-20">
                        <p class="text-gray font-14">{{ trans('cart.coupon_code_hint') }}</p>

                        @if(!empty($userGroup) and !empty($userGroup->discount))
                            <p class="text-gray mt-25">{{ trans('cart.in_user_group',['group_name' => $userGroup->name , 'percent' => $userGroup->discount]) }}</p>
                        @endif

                        <form action="/carts/coupon/validate" method="Post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <input type="text" name="coupon" id="coupon_input" class="form-control mt-25"
                                       placeholder="{{ trans('cart.enter_your_code_here') }}">
                                <span class="invalid-feedback">{{ trans('cart.coupon_invalid') }}</span>
                                <span class="valid-feedback">{{ trans('cart.coupon_valid') }}</span>
                            </div>

                            <button type="submit" id="checkCoupon"
                                    class="btn btn-sm btn-primary mt-50">{{ trans('cart.validate') }}</button>
                        </form>
                    </div>
                </section>
            </div>

            <div class="col-12 col-lg-6">
                <section class="mt-45">
                    <h3 class="section-title">{{ trans('cart.cart_totals') }}</h3>
                    <div class="rounded-sm shadow mt-20 pb-20 px-20">

                        <div class="cart-checkout-item">
                            <h4 class="text-secondary font-14 font-weight-500">{{ trans('cart.sub_total') }}</h4>
                            <span class="font-14 text-gray font-weight-bold">{{ $currency }}{{ $subTotal }}</span>
                        </div>

                        <div class="cart-checkout-item">
                            <h4 class="text-secondary font-14 font-weight-500">{{ trans('public.discount') }}</h4>
                            <span class="font-14 text-gray font-weight-bold">{{ $currency }}<span id="totalDiscount">{{ $totalDiscount }}</span></span>
                        </div>

                        <div class="cart-checkout-item">
                            <h4 class="text-secondary font-14 font-weight-500">{{ trans('cart.tax') }}
                                <span class="font-14 text-gray ">({{ $tax }}%)</span>
                            </h4>
                            <span class="font-14 text-gray font-weight-bold">{{ $currency }}<span id="taxPrice">{{ $taxPrice }}</span></span>
                        </div>

                        <div class="cart-checkout-item border-0">
                            <h4 class="text-secondary font-14 font-weight-500">{{ trans('cart.total') }}</h4>
                            <span class="font-14 text-gray font-weight-bold">{{ $currency }}<span id="totalAmount">{{ $total }}</span></span>
                        </div>

                        <form action="/cart/checkout" method="post" id="cartForm">
                            {{ csrf_field() }}
                            <input type="hidden" name="discount_id" value="">

                            <button type="submit" class="btn btn-sm btn-primary mt-15">{{ trans('cart.checkout') }}</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/parts/cart.min.js"></script>
@endpush
