@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')

@endpush

@section('content')

    <section>
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="section-title">{{ trans('panel.favorite_live_classes') }}</h2>
        </div>

        @if(!empty($favorites) and !$favorites->isEmpty())

            @foreach($favorites as $favorite)
                <div class="row mt-30">
                    <div class="col-12">
                        <div class="webinar-card webinar-list d-flex">
                            <div class="image-box">
                                <img src="{{ $favorite->webinar->getImage() }}" class="img-cover" alt="">

                                @if($favorite->webinar->type == 'webinar')
                                    <div class="progress">
                                        <span class="progress-bar" style="width: {{ $favorite->webinar->getProgress() }}%"></span>
                                    </div>
                                @endif
                            </div>

                            <div class="webinar-card-body w-100 d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="{{ $favorite->webinar->getUrl() }}" target="_blank">
                                        <h3 class="font-16 text-dark-blue font-weight-bold">{{ $favorite->webinar->title }}</h3>
                                    </a>

                                    <div class="btn-group dropdown table-actions">
                                        <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i data-feather="more-vertical" height="20"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="/panel/webinars/favorites/{{ $favorite->id }}/delete" class="webinar-actions d-block delete-action">{{ trans('public.remove') }}</a>
                                        </div>
                                    </div>
                                </div>

                                @include(getTemplate() . '.includes.webinar.rate',['rate' => $favorite->webinar->getRate()])

                                <div class="webinar-price-box mt-15">
                                    @if($favorite->webinar->bestTicket() < $favorite->webinar->price)
                                        <span class="real">{{ $currency }}{{ number_format($favorite->webinar->bestTicket(),2) }}</span>
                                        <span class="off ml-10">{{ $currency }}{{ number_format($favorite->webinar->price,2) }}</span>
                                    @else
                                        <span class="real">{{ $currency }}{{ number_format($favorite->webinar->price,2) }}</span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center justify-content-between flex-wrap mt-auto">
                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.item_id') }}:</span>
                                        <span class="stat-value">{{ $favorite->webinar->id }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.category') }}:</span>
                                        <span class="stat-value">{{ !empty($favorite->webinar->category_id) ? $favorite->webinar->category->title : '' }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.duration') }}:</span>
                                        <span class="stat-value">{{ convertMinutesToHourAndMinute($favorite->webinar->duration) }} {{ trans('home.hours') }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.start_date') }}:</span>
                                        <span class="stat-value">{{ dateTimeFormat($favorite->webinar->start_date,'j F Y') }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.instructor') }}:</span>
                                        <span class="stat-value">{{ $favorite->webinar->teacher->full_name }}</span>
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
                'title' => trans('panel.no_result_favorites'),
                'hint' =>  trans('panel.no_result_favorites_hint') ,
            ])
        @endif

    </section>

    <div class="my-30">
        {{ $favorites->links('vendor.pagination.panel') }}
    </div>
@endsection
