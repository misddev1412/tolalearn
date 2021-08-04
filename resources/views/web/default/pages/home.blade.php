@extends(getTemplate().'.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/owl-carousel2/owl.carousel.min.css">
@endpush

@section('content')

    @if(!empty($heroSectionData))

        @if(!empty($heroSectionData['has_lottie']) and $heroSectionData['has_lottie'] == "1")
            @push('scripts_bottom')
                <script src="/assets/default/vendors/lottie/lottie-player.js"></script>
            @endpush
        @endif

        <section class="slider-container  {{ ($heroSection == "2") ? 'slider-hero-section2' : '' }}" style="background-image: url('{{ $heroSectionData['hero_background'] }}')">

            @if($heroSection == "1")
                <div class="mask"></div>
            @endif

            <div class="container user-select-none">

                @if($heroSection == "2")
                    <div class="row slider-content align-items-center hero-section2 flex-column-reverse flex-md-row">
                        <div class="col-12 col-md-7 col-lg-6">
                            <h1 class="text-secondary font-weight-bold">{{ $heroSectionData['title'] }}</h1>
                            <p class="slide-hint text-gray mt-20">{{ nl2br($heroSectionData['description']) }}</p>

                            <form action="/search" method="get" class="d-inline-flex mt-30 mt-lg-30 w-100">
                                <div class="form-group d-flex align-items-center m-0 slider-search p-10 bg-white w-100">
                                    <input type="text" name="search" class="form-control border-0 mr-lg-50" placeholder="{{ trans('home.slider_search_placeholder') }}"/>
                                    <button type="submit" class="btn btn-primary rounded-pill">{{ trans('home.find') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-12 col-md-5 col-lg-6">
                            @if(!empty($heroSectionData['has_lottie']) and $heroSectionData['has_lottie'] == "1")
                                <lottie-player src="{{ $heroSectionData['hero_vector'] }}" background="transparent" speed="1" class="w-100" loop autoplay></lottie-player>
                            @else
                                <img src="{{ $heroSectionData['hero_vector'] }}" alt="{{ $heroSectionData['title'] }}" class="img-cover">
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center slider-content">
                        <h1>{{ $heroSectionData['title'] }}</h1>
                        <div class="row h-100 align-items-center justify-content-center text-center">
                            <div class="col-12 col-md-9 col-lg-7">
                                <p class="mt-30 slide-hint">{{ nl2br($heroSectionData['description']) }}</p>

                                <form action="/search" method="get" class="d-inline-flex mt-30 mt-lg-50 w-100">
                                    <div class="form-group d-flex align-items-center m-0 slider-search p-10 bg-white w-100">
                                        <input type="text" name="search" class="form-control border-0 mr-lg-50" placeholder="{{ trans('home.slider_search_placeholder') }}"/>
                                        <button type="submit" class="btn btn-primary rounded-pill">{{ trans('home.find') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <div class="stats-container">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                    <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                        <div class="stat-icon-box teacher">
                            <img src="/assets/default/img/stats/teacher.svg" alt=""/>
                        </div>
                        <strong class="stat-number mt-10">{{ $skillfulTeachersCount }}</strong>
                        <h4 class="stat-title">{{ trans('home.skillful_teachers') }}</h4>
                        <p class="stat-desc mt-10">{{ trans('home.skillful_teachers_hint') }}</p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                    <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                        <div class="stat-icon-box student">
                            <img src="/assets/default/img/stats/student.svg" alt=""/>
                        </div>
                        <strong class="stat-number mt-10">{{ $studentsCount }}</strong>
                        <h4 class="stat-title">{{ trans('home.happy_students') }}</h4>
                        <p class="stat-desc mt-10">{{ trans('home.happy_students_hint') }}</p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                    <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                        <div class="stat-icon-box video">
                            <img src="/assets/default/img/stats/video.svg" alt=""/>
                        </div>
                        <strong class="stat-number mt-10">{{ $liveClassCount }}</strong>
                        <h4 class="stat-title">{{ trans('home.live_classes') }}</h4>
                        <p class="stat-desc mt-10">{{ trans('home.live_classes_hint') }}</p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                    <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                        <div class="stat-icon-box course">
                            <img src="/assets/default/img/stats/course.svg" alt=""/>
                        </div>
                        <strong class="stat-number mt-10">{{ $offlineCourseCount }}</strong>
                        <h4 class="stat-title">{{ trans('home.offline_courses') }}</h4>
                        <p class="stat-desc mt-10">{{ trans('home.offline_courses_hint') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($featureWebinars) and !$featureWebinars->isEmpty())
        <section class="home-sections container mt-0 ">
            <div class="px-20 px-md-0">
                <h2 class="section-title">{{ trans('home.featured_classes') }}</h2>
                <p class="section-hint">{{ trans('home.featured_classes_hint') }}</p>
            </div>

            <div class="feature-slider-container position-relative d-flex justify-content-center mt-10">
                <div class="swiper-container features-swiper-container pb-25">
                    <div class="swiper-wrapper py-10">
                        @foreach($featureWebinars as $feature)
                            <div class="swiper-slide">
                                <div class="feature-slider d-flex h-100" style="background-image: url('{{ $feature->webinar->getImage() }}')">
                                    <div class="mask"></div>
                                    <div class="p-5 p-md-25 feature-slider-card">
                                        <div class="d-flex flex-column feature-slider-body position-relative h-100">
                                            <a href="{{ $feature->webinar->getUrl() }}">
                                                <h3 class="card-title">{{ $feature->webinar->title }}</h3>
                                            </a>

                                            <div class="user-inline-avatar mt-15 d-flex align-items-center">
                                                <div class="avatar">
                                                    <img src="{{ $feature->webinar->teacher->getAvatar() }}" class="img-cover" alt="{{ $feature->webinar->teacher->full_naem }}">
                                                </div>
                                                <a href="{{ $feature->webinar->teacher->getProfileUrl() }}" target="_blank" class="user-name font-14 ml-5">{{ $feature->webinar->teacher->full_name }}</a>
                                            </div>

                                            <p class="mt-25 feature-desc text-gray">{{ $feature->description }}</p>

                                            @include('web.default.includes.webinar.rate',['rate' => $feature->webinar->getRate()])

                                            <div class="feature-footer mt-auto d-flex align-items-center justify-content-between">
                                                <div class="d-flex justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <i data-feather="clock" width="20" height="20" class="webinar-icon"></i>
                                                        <span class="duration ml-5 text-dark-blue font-14">{{ convertMinutesToHourAndMinute($feature->webinar->duration) }} {{ trans('home.hours') }}</span>
                                                    </div>

                                                    <div class="vertical-line mx-10"></div>

                                                    <div class="d-flex align-items-center">
                                                        <i data-feather="calendar" width="20" height="20" class="webinar-icon"></i>
                                                        <span class="date-published ml-5 text-dark-blue font-14">{{ dateTimeFormat($feature->webinar->start_date, 'j F Y') }}</span>
                                                    </div>
                                                </div>

                                                <div class="feature-price-box">
                                                    @if(!empty($feature->webinar->price ) and $feature->webinar->price > 0)
                                                        {{ $currency }}{{ number_format($feature->webinar->price,2) }}
                                                    @else
                                                        {{ trans('public.free') }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="swiper-pagination features-swiper-pagination"></div>
            </div>
        </section>
    @endif

    @if(!empty($latestWebinars) and !$latestWebinars->isEmpty())
        <section class="home-sections home-sections-swiper container">
            <div class="d-flex justify-content-between ">
                <div>
                    <h2 class="section-title">{{ trans('home.latest_classes') }}</h2>
                    <p class="section-hint">{{ trans('home.latest_webinars_hint') }}</p>
                </div>

                <a href="/classes?sort=newest" class="btn btn-border-white">{{ trans('home.view_all') }}</a>
            </div>

            <div class="mt-10 position-relative">
                <div class="swiper-container latest-webinars-swiper px-12">
                    <div class="swiper-wrapper py-20">
                        @foreach($latestWebinars as $latestWebinar)
                            <div class="swiper-slide">
                                @include('web.default.includes.webinar.grid-card',['webinar' => $latestWebinar])
                            </div>
                        @endforeach

                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="swiper-pagination latest-webinars-swiper-pagination"></div>
                </div>
            </div>
        </section>
    @endif

    @if(!empty($bestRateWebinars) and !$bestRateWebinars->isEmpty())
        <section class="home-sections container">
            <div class="d-flex justify-content-between">
                <div>
                    <h2 class="section-title">{{ trans('home.best_rates') }}</h2>
                    <p class="section-hint">{{ trans('home.best_rates_hint') }}</p>
                </div>

                <a href="/classes?sort=best_rates" class="btn btn-border-white">{{ trans('home.view_all') }}</a>
            </div>

            <div class="mt-10 position-relative">
                <div class="swiper-container best-rates-webinars-swiper px-12">
                    <div class="swiper-wrapper py-20">
                        @foreach($bestRateWebinars as $bestRateWebinar)
                            <div class="swiper-slide">
                                @include('web.default.includes.webinar.grid-card',['webinar' => $bestRateWebinar])
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="swiper-pagination best-rates-webinars-swiper-pagination"></div>
                </div>
            </div>
        </section>
    @endif

    @if(!empty($trendCategories) and !$trendCategories->isEmpty())
        <section class="home-sections home-sections-swiper container">
            <h2 class="section-title">{{ trans('home.trending_categories') }}</h2>
            <p class="section-hint">{{ trans('home.trending_categories_hint') }}</p>

            <div class="row mt-40">

                @foreach($trendCategories as $trend)
                    <div class="col-6 col-md-3 col-lg-2 mt-20 mt-md-0">
                        <a href="{{ $trend->category->getUrl() }}">
                            <div class="trending-card d-flex flex-column align-items-center w-100">
                                <div class="trending-image d-flex align-items-center justify-content-center w-100" style="background-color: {{ $trend->color }}">
                                    <div class="icon mb-3">
                                        <img src="{{ $trend->getIcon() }}" width="10" class="img-cover" alt="{{ $trend->category->title }}">
                                    </div>
                                </div>

                                <div class="item-count px-10 px-lg-20 py-5 py-lg-10">{{ $trend->category->webinars_count }} {{ trans('product.course') }}</div>

                                <h3>{{ $trend->category->title }}</h3>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Ads Bannaer --}}
    @if(!empty($advertisingBanners1) and count($advertisingBanners1))
        <div class="home-sections container">
            <div class="row">
                @foreach($advertisingBanners1 as $banner1)
                    <div class="col-{{ $banner1->size }}">
                        <a href="{{ $banner1->link }}">
                            <img src="{{ $banner1->image }}" class="img-cover rounded-sm" alt="{{ $banner1->title }}">
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    {{-- ./ Ads Bannaer --}}

    @if(!empty($bestSaleWebinars) and !$bestSaleWebinars->isEmpty())
        <section class="home-sections container">
            <div class="d-flex justify-content-between">
                <div>
                    <h2 class="section-title">{{ trans('home.best_sellers') }}</h2>
                    <p class="section-hint">{{ trans('home.best_sellers_hint') }}</p>
                </div>

                <a href="/classes?sort=bestsellers" class="btn btn-border-white">{{ trans('home.view_all') }}</a>
            </div>

            <div class="mt-10 position-relative">
                <div class="swiper-container best-sales-webinars-swiper px-12">
                    <div class="swiper-wrapper py-20">
                        @foreach($bestSaleWebinars as $bestSaleWebinar)
                            <div class="swiper-slide">
                                @include('web.default.includes.webinar.grid-card',['webinar' => $bestSaleWebinar])
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="swiper-pagination best-sales-webinars-swiper-pagination"></div>
                </div>
            </div>
        </section>
    @endif

    @if(!empty($hasDiscountWebinars) and !$hasDiscountWebinars->isEmpty())
        <section class="home-sections container">
            <div class="d-flex justify-content-between">
                <div>
                    <h2 class="section-title">{{ trans('home.discount_classes') }}</h2>
                    <p class="section-hint">{{ trans('home.discount_classes_hint') }}</p>
                </div>

                <a href="/classes?discount=on" class="btn btn-border-white">{{ trans('home.view_all') }}</a>
            </div>

            <div class="mt-10 position-relative">
                <div class="swiper-container has-discount-webinars-swiper px-12">
                    <div class="swiper-wrapper py-20">
                        @foreach($hasDiscountWebinars as $hasDiscountWebinar)
                            <div class="swiper-slide">
                                @include('web.default.includes.webinar.grid-card',['webinar' => $hasDiscountWebinar])
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="swiper-pagination has-discount-webinars-swiper-pagination"></div>
                </div>
            </div>
        </section>
    @endif

    @if(!empty($freeWebinars) and !$freeWebinars->isEmpty())
        <section class="home-sections home-sections-swiper container">
            <div class="d-flex justify-content-between">
                <div>
                    <h2 class="section-title">{{ trans('home.free_classes') }}</h2>
                    <p class="section-hint">{{ trans('home.free_classes_hint') }}</p>
                </div>

                <a href="/classes?free=on" class="btn btn-border-white">{{ trans('home.view_all') }}</a>
            </div>

            <div class="mt-10 position-relative">
                <div class="swiper-container free-webinars-swiper px-12">
                    <div class="swiper-wrapper py-20">

                        @foreach($freeWebinars as $freeWebinar)
                            <div class="swiper-slide">
                                @include('web.default.includes.webinar.grid-card',['webinar' => $freeWebinar])
                            </div>
                        @endforeach

                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="swiper-pagination free-webinars-swiper-pagination"></div>
                </div>
            </div>
        </section>
    @endif

    @if(!empty($testimonials) and !$testimonials->isEmpty())
        <div class="position-relative testimonials-container">

            <div id="parallax1" class="ltr">
                <div data-depth="0.2" class="gradient-box left-gradient-box"></div>
            </div>

            <section class="container home-sections home-sections-swiper">
                <div class="text-center">
                    <h2 class="section-title">{{ trans('home.testimonials') }}</h2>
                    <p class="section-hint">{{ trans('home.testimonials_hint') }}</p>
                </div>

                <div class="position-relative">
                    <div class="swiper-container testimonials-swiper px-12">
                        <div class="swiper-wrapper">

                            @foreach($testimonials as $testimonial)
                                <div class="swiper-slide">
                                    <div class="testimonials-card position-relative py-15 py-lg-30 px-10 px-lg-20 rounded-sm shadow bg-white text-center">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="testimonials-user-avatar">
                                                <img src="{{ $testimonial->user_avatar }}" alt="{{ $testimonial->user_name }}" class="img-cover rounded-circle">
                                            </div>
                                            <h4 class="font-16 font-weight-bold text-secondary mt-30">{{ $testimonial->user_name }}</h4>
                                            <span class="d-block font-14 text-gray">{{ $testimonial->user_bio }}</span>
                                            @include('web.default.includes.webinar.rate',['rate' => $testimonial->rate, 'dontShowRate' => true])
                                        </div>

                                        <p class="mt-25 text-gray font-14">{{ nl2br($testimonial->comment) }}</p>

                                        <div class="bottom-gradient"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>

                    <div class="d-flex justify-content-center">
                        <div class="swiper-pagination testimonials-swiper-pagination"></div>
                    </div>
                </div>
            </section>

            <div id="parallax2" class="ltr">
                <div data-depth="0.4" class="gradient-box right-gradient-box"></div>
            </div>

            <div id="parallax3" class="ltr">
                <div data-depth="0.8" class="gradient-box bottom-gradient-box"></div>
            </div>
        </div>
    @endif

    @if(!empty($subscribes) and !$subscribes->isEmpty())
        <div class="position-relative subscribes-container pe-none user-select-none">
            <div id="parallax4" class="ltr">
                <div data-depth="0.2" class="gradient-box left-gradient-box"></div>
            </div>

            <section class="container home-sections home-sections-swiper">
                <div class="text-center">
                    <h2 class="section-title">{{ trans('home.subscribe_now') }}</h2>
                    <p class="section-hint">{{ trans('home.subscribe_now_hint') }}</p>
                </div>

                <div class="position-relative mt-30">
                    <div class="swiper-container subscribes-swiper px-12">
                        <div class="swiper-wrapper py-20">

                            @foreach($subscribes as $subscribe)
                                <div class="swiper-slide">
                                    <div class="subscribe-plan position-relative bg-white d-flex flex-column align-items-center rounded-sm shadow pt-50 pb-20 px-20">
                                        @if($subscribe->is_popular)
                                            <span class="badge badge-primary badge-popular px-15 py-5">{{ trans('panel.popular') }}</span>
                                        @endif

                                        <div class="plan-icon">
                                            <img src="{{ $subscribe->icon }}" class="img-cover" alt="">
                                        </div>

                                        <h3 class="mt-20 font-30 text-secondary">{{ $subscribe->title }}</h3>
                                        <p class="font-weight-500 text-gray mt-10">{{ $subscribe->description }}</p>

                                        <div class="d-flex align-items-start text-primary mt-30">
                                            <span class="font-20 mr-5">$</span>
                                            <span class="font-36 line-height-1">{{ $subscribe->price }}</span>
                                        </div>

                                        <ul class="mt-20 plan-feature">
                                            <li class="mt-10">{{ $subscribe->days }} {{ trans('financial.days_of_subscription') }}</li>
                                            <li class="mt-10">{{ $subscribe->usable_count }} {{ trans('home.downloads') }}</li>
                                        </ul>

                                        @if(auth()->check())
                                            <form action="/panel/financial/pay-subscribes" method="post" class="w-100">
                                                {{ csrf_field() }}
                                                <input name="amount" value="{{ $subscribe->price }}" type="hidden">
                                                <input name="id" value="{{ $subscribe->id }}" type="hidden">
                                                <button type="submit" class="btn btn-primary btn-block mt-50">{{ trans('financial.purchase') }}</button>
                                            </form>
                                        @else
                                            <a href="/login" class="btn btn-primary btn-block mt-50">{{ trans('financial.purchase') }}</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                    <div class="d-flex justify-content-center">
                        <div class="swiper-pagination subscribes-swiper-pagination"></div>
                    </div>

                </div>
            </section>

            <div id="parallax5" class="ltr">
                <div data-depth="0.4" class="gradient-box right-gradient-box"></div>
            </div>

            <div id="parallax6" class="ltr">
                <div data-depth="0.6" class="gradient-box bottom-gradient-box"></div>
            </div>
        </div>
    @endif

    @if(!empty($boxVideoOrImage))
        <section class="home-sections home-sections-swiper position-relative">
            <div class="home-video-mask"></div>
            <div class="container home-video-container d-flex flex-column align-items-center justify-content-center position-relative" style="background-image: url('{{ $boxVideoOrImage['background'] ?? '' }}')">
                <a href="{{ $boxVideoOrImage['link'] ?? '' }}" class="home-video-play-button d-flex align-items-center justify-content-center position-relative">
                    <i data-feather="play" width="36" height="36" class=""></i>
                </a>

                <div class="mt-50 pt-10 text-center">
                    <h2 class="home-video-title">{{ $boxVideoOrImage['title'] ?? '' }}</h2>
                    <p class="home-video-hint mt-10">{{ $boxVideoOrImage['description'] ?? '' }}</p>
                </div>
            </div>
        </section>
    @endif

    @if(!empty($instructors) and !$instructors->isEmpty())
        <section class="home-sections container">
            <div class="d-flex justify-content-between">
                <div>
                    <h2 class="section-title">{{ trans('home.instructors') }}</h2>
                    <p class="section-hint">{{ trans('home.instructors_hint') }}</p>
                </div>

                <a href="/instructors" class="btn btn-border-white">{{ trans('home.all_instructors') }}</a>
            </div>

            <div class="position-relative mt-20 ltr">
                <div class="owl-carousel customers-testimonials instructors-swiper-container">

                    @foreach($instructors as $instructor)
                        <div class="item">
                            <div class="shadow-effect">
                                <div class="instructors-card d-flex flex-column align-items-center justify-content-center">
                                    <div class="instructors-card-avatar">
                                        <img src="{{ $instructor->getAvatar() }}" alt="{{ $instructor->full_name }}" class="rounded-circle img-cover">
                                    </div>
                                    <div class="instructors-card-info mt-10 text-center">
                                        <a href="{{ $instructor->getProfileUrl() }}" target="_blank">
                                            <h3 class="font-16 font-weight-bold text-dark-blue">{{ $instructor->full_name }}</h3>
                                        </a>

                                        <p class="font-14 text-gray mt-5">{{ $instructor->bio }}</p>
                                        <div class="stars-card d-flex align-items-center justify-content-center mt-10">
                                            @php
                                                $i = 5;
                                            @endphp
                                            @while(--$i >= 5 - $instructor->rates())
                                                <i data-feather="star" width="20" height="20" class="active"></i>
                                            @endwhile
                                            @while($i-- >= 0)
                                                <i data-feather="star" width="20" height="20" class=""></i>
                                            @endwhile
                                        </div>

                                        @if(!empty($instructor->hasMeeting()))
                                            <a href="{{ $instructor->getProfileUrl() }}?tab=appointments" class="btn btn-primary btn-sm rounded-pill mt-15">{{ trans('home.reserve_a_live_class') }}</a>
                                        @else
                                            <a href="{{ $instructor->getProfileUrl() }}" class="btn btn-primary btn-sm rounded-pill mt-15">{{ trans('public.profile') }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    @endif

    {{-- Ads Bannaer --}}
    @if(!empty($advertisingBanners2) and count($advertisingBanners2))
        <div class="home-sections container">
            <div class="row">
                @foreach($advertisingBanners2 as $banner2)
                    <div class="col-{{ $banner2->size }}">
                        <a href="{{ $banner2->link }}">
                            <img src="{{ $banner2->image }}" class="img-cover rounded-sm" alt="{{ $banner2->title }}">
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    {{-- ./ Ads Bannaer --}}

    @if(!empty($organizations) and !$organizations->isEmpty())
        <section class="home-sections home-sections-swiper container">
            <div class="d-flex justify-content-between">
                <div>
                    <h2 class="section-title">{{ trans('home.organizations') }}</h2>
                    <p class="section-hint">{{ trans('home.organizations_hint') }}</p>
                </div>

                <a href="/organizations" class="btn btn-border-white">{{ trans('home.all_organizations') }}</a>
            </div>

            <div class="position-relative mt-20">
                <div class="swiper-container organization-swiper-container px-12">
                    <div class="swiper-wrapper py-20">

                        @foreach($organizations as $organization)
                            <div class="swiper-slide">
                                <div class="home-organizations-card d-flex flex-column align-items-center justify-content-center">
                                    <div class="home-organizations-avatar">
                                        <img src="{{ $organization->getAvatar() }}" class="img-cover rounded-circle" alt="{{ $organization->full_name }}">
                                    </div>
                                    <a href="{{ $organization->getProfileUrl() }}" class="mt-25 d-flex flex-column align-items-center justify-content-center">
                                        <h3 class="home-organizations-title">{{ $organization->full_name }}</h3>
                                        <p class="home-organizations-desc mt-10">{{ $organization->bio }}</p>
                                        <span class="home-organizations-badge badge mt-15">{{ $organization->webinars_count }} {{ trans('webinars.classes') }}</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="swiper-pagination organization-swiper-pagination"></div>
                </div>
            </div>
        </section>
    @endif

    @if(!empty($blog) and !$blog->isEmpty())
        <section class="home-sections container">
            <div class="d-flex justify-content-between">
                <div>
                    <h2 class="section-title">{{ trans('home.blog') }}</h2>
                    <p class="section-hint">{{ trans('home.blog_hint') }}</p>
                </div>

                <a href="/blog" class="btn btn-border-white">{{ trans('home.all_blog') }}</a>
            </div>

            <div class="row mt-35">

                @foreach($blog as $post)
                    <div class="col-12 col-md-4 col-lg-4 mt-20 mt-lg-0">
                        @include('web.default.blog.grid-list',['post' =>$post])
                    </div>
                @endforeach

            </div>
        </section>
    @endif
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/swiper/swiper-bundle.min.js"></script>
    <script src="/assets/default/vendors/owl-carousel2/owl.carousel.min.js"></script>
    <script src="/assets/default/vendors/parallax/parallax.min.js"></script>
    <script src="/assets/default/js/parts/home.min.js"></script>
@endpush
