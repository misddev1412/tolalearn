@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')

@endpush

@section('content')
    <section>
        <h2 class="section-title">{{ trans('panel.my_activity') }}</h2>

        <div class="activities-container mt-25 p-20 p-lg-35">
            <div class="row">
                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/webinars.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $allWebinarsCount }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('panel.purchased') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/hours.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ convertMinutesToHourAndMinute($hours) }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('home.hours') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/upcoming.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $upComing }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('panel.upcoming') }}</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="mt-25">
        <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
            <h2 class="section-title">{{ trans('panel.my_purchases') }}</h2>

            <form action="" method="get">
                <div class="d-flex align-items-center flex-row-reverse flex-md-row justify-content-start justify-content-md-center mt-20 mt-md-0">
                    <label class="mb-0 mr-10 text-gray font-14 font-weight-500" for="conductedSwitch">{{ trans('panel.only_not_conducted_webinars') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="not_conducted" @if(request()->get('not_conducted','') == 'on') checked @endif class="custom-control-input" id="conductedSwitch">
                        <label class="custom-control-label cursor-pointer" for="conductedSwitch"></label>
                    </div>
                </div>
            </form>
        </div>

        @if(!empty($webinars) and !$webinars->isEmpty())
            @foreach($webinars as $webinar)
                @php
                    $lastSession = $webinar->lastSession();
                    $nextSession = $webinar->nextSession();
                    $isProgressing = false;

                    if($webinar->start_date <= time() and !empty($lastSession) and $lastSession->date > time()) {
                        $isProgressing=true;
                    }
                @endphp

                <div class="row mt-30">
                    <div class="col-12">
                        <div class="webinar-card webinar-list d-flex">
                            <div class="image-box">
                                <img src="{{ $webinar->getImage() }}" class="img-cover" alt="">

                                @if($webinar->type == 'webinar')
                                    @if($webinar->start_date > time())
                                        <span class="badge badge-primary">{{  trans('panel.not_conducted') }}</span>
                                    @elseif($webinar->isProgressing())
                                        <span class="badge badge-secondary">{{ trans('webinars.in_progress') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ trans('public.finished') }}</span>
                                    @endif
                                @elseif(!empty($webinar->downloadable))
                                    <span class="badge badge-secondary">{{ trans('home.downloadable') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ trans('webinars.'.$webinar->type) }}</span>
                                @endif

                                @php
                                    $percent = $webinar->getProgress();

                                    if($webinar->isWebinar()){
                                        if($webinar->isProgressing()) {
                                            $progressTitle = trans('public.course_learning_passed',['percent' => $percent]);
                                        } else {
                                            $progressTitle = $webinar->sales_count .'/'. $webinar->capacity .' '. trans('quiz.students');
                                        }
                                    } else {
                                           $progressTitle = trans('public.course_learning_passed',['percent' => $percent]);
                                    }
                                @endphp

                                <div class="progress cursor-pointer" data-toggle="tooltip" data-placement="top" title="{{ $progressTitle }}">
                                    <span class="progress-bar" style="width: {{ $percent }}%"></span>
                                </div>
                            </div>

                            <div class="webinar-card-body w-100 d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="{{ $webinar->getUrl() }}">
                                        <h3 class="webinar-title font-weight-bold font-16 text-dark-blue">
                                            {{ $webinar->title }}
                                            <span class="badge badge-dark ml-10 status-badge-dark">{{ trans('webinars.'.$webinar->type) }}</span>
                                        </h3>
                                    </a>

                                    <div class="btn-group dropdown table-actions">
                                        <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i data-feather="more-vertical" height="20"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            @if(!empty($webinar->start_date) and ($webinar->start_date > time() or ($webinar->isProgressing() and !empty($nextSession))))
                                                <button type="button" data-webinar-id="{{ $webinar->id }}" class="join-purchase-webinar webinar-actions btn-transparent d-block">{{ trans('footer.join') }}</button>
                                            @endif

                                            @if(!empty($webinar->downloadable) or (!empty($webinar->files) and count($webinar->files)))
                                                <a href="{{ $webinar->getUrl() }}?tab=content" target="_blank" class="webinar-actions d-block mt-10">{{ trans('home.download') }}</a>
                                            @endif

                                            @if($webinar->price > 0)
                                                <a href="/panel/webinars/{{ $webinar->id }}/invoice" target="_blank" class="webinar-actions d-block mt-10">{{ trans('public.invoice') }}</a>
                                            @endif

                                            <a href="{{ $webinar->getUrl() }}?tab=reviews" target="_blank" class="webinar-actions d-block mt-10">{{ trans('public.feedback') }}</a>
                                        </div>
                                    </div>
                                </div>

                                @include(getTemplate() . '.includes.webinar.rate',['rate' => $webinar->getRate()])

                                <div class="webinar-price-box mt-15">
                                    @if($webinar->price > 0)
                                        @if($webinar->bestTicket() < $webinar->price)
                                            <span class="real">{{ $currency }}{{ number_format($webinar->bestTicket(),2) }}</span>
                                            <span class="off ml-10">{{ $currency }}{{ number_format($webinar->price,2) }}</span>
                                        @else
                                            <span class="real">{{ $currency }}{{ number_format($webinar->price,2) }}</span>
                                        @endif
                                    @else
                                        <span class="real">{{ trans('public.free') }}</span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center justify-content-between flex-wrap mt-auto">
                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.item_id') }}:</span>
                                        <span class="stat-value">{{ $webinar->id }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.category') }}:</span>
                                        <span class="stat-value">{{ !empty($webinar->category_id) ? $webinar->category->title : '' }}</span>
                                    </div>

                                    @if($webinar->type == 'webinar')
                                        @if($webinar->isProgressing() and !empty($nextSession))
                                            <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                                <span class="stat-title">{{ trans('webinars.next_session_duration') }}:</span>
                                                <span class="stat-value">{{ convertMinutesToHourAndMinute($nextSession->duration) }} Hrs</span>
                                            </div>

                                            <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                                <span class="stat-title">{{ trans('webinars.next_session_start_date') }}:</span>
                                                <span class="stat-value">{{ dateTimeFormat($nextSession->date,'j F Y') }}</span>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                                <span class="stat-title">{{ trans('public.duration') }}:</span>
                                                <span class="stat-value">{{ convertMinutesToHourAndMinute($webinar->duration) }} Hrs</span>
                                            </div>

                                            <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                                <span class="stat-title">{{ trans('public.start_date') }}:</span>
                                                <span class="stat-value">{{ dateTimeFormat($webinar->start_date,'j F Y') }}</span>
                                            </div>
                                        @endif
                                    @endif

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.instructor') }}:</span>
                                        <span class="stat-value">{{ $webinar->teacher->full_name }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('panel.purchase_date') }}:</span>
                                        <span class="stat-value">{{ dateTimeFormat($webinar->purchast_date,'j F Y') }}</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            @include(getTemplate() . '.includes.no-result',[
            'file_name' => 'student.png',
            'title' => trans('panel.no_result_purchases') ,
            'hint' => trans('panel.no_result_purchases_hint') ,
            'btn' => ['url' => '/classes?sort=newest','text' => trans('panel.start_learning')]
        ])
        @endif
    </section>

    <div class="my-30">
        {{ $webinars->links('vendor.pagination.panel') }}
    </div>

    @include('web.default.panel.webinar.join_webinar_modal')
@endsection

@push('scripts_bottom')
    <script>
        ;(function (){ 
        'use strict'
        var undefinedActiveSessionLang = '{{ trans('webinars.undefined_active_session') }}';
        }())           
    </script>

    <script src="/assets/default/js/panel/join_webinar.min.js"></script>
@endpush
