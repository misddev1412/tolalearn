@extends(getTemplate() .'.panel.layouts.panel_layout')

@section('content')
    <h2 class="section-title">{{ trans('financial.my_active_plan') }}</h2>


    @if($activeSubscribe)
        <section>
            <div class="activities-container mt-25 p-20 p-lg-35">
                <div class="row">
                    <div class="col-4 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/webinars.svg" width="64" height="64" alt="">
                            <strong class="font-30 font-weight-bold mt-5">{{ $activeSubscribe->title }}</strong>
                            <span class="font-16 text-gray font-weight-500">{{ trans('financial.active_plan') }}</span>
                        </div>
                    </div>

                    <div class="col-4 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/53.svg" width="64" height="64" alt="">
                            <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $activeSubscribe->usable_count - $activeSubscribe->used_count }}</strong>
                            <span class="font-16 text-gray font-weight-500">{{ trans('financial.remained_downloads') }}</span>
                        </div>
                    </div>

                    <div class="col-4 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/54.svg" width="64" height="64" alt="">
                            <strong class="font-30 text-dark-blue text-dark-blue font-weight-bold mt-5">{{ $activeSubscribe->days - $dayOfUse }}</strong>
                            <span class="font-16 text-gray font-weight-500">{{ trans('financial.days_remained') }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    @else
        @include(getTemplate() . '.includes.no-result',[
           'file_name' => 'subcribe.png',
           'title' => trans('financial.subcribe_no_result'),
           'hint' => nl2br(trans('financial.subcribe_no_result_hint')),
       ])
    @endif

    <section class="mt-30">
        <h2 class="section-title">{{ trans('financial.select_a_subscribe_plan') }}</h2>

        <div class="row mt-15">

            @foreach($subscribes as $subscribe)
                <div class="col-12 col-sm-6 col-lg-3 mt-15">
                    <div class="subscribe-plan position-relative bg-white d-flex flex-column align-items-center rounded-sm shadow pt-50 pb-20 px-20">
                        @if($subscribe->is_popular)
                            <span class="badge badge-primary badge-popular px-15 py-5">{{ trans('panel.popular') }}</span>
                        @endif

                        <div class="plan-icon">
                            <img src="{{ $subscribe->icon }}" class="img-cover" alt="">
                        </div>

                        <h3 class="mt-20 font-30 text-secondary">{{ $subscribe->title }}</h3>
                        <p class="font-weight-500 font-14 text-gray mt-10">{{ $subscribe->description }}</p>

                        <div class="d-flex align-items-start text-primary mt-30">
                            <span class="font-20 mr-5">$</span>
                            <span class="font-36 line-height-1">{{ $subscribe->price }}</span>
                        </div>

                        <ul class="mt-20 plan-feature">
                            <li class="mt-10">{{ $subscribe->days }} {{ trans('financial.days_of_subscription') }}</li>
                            <li class="mt-10">{{ $subscribe->usable_count }} {{ trans('home.downloads') }}</li>
                        </ul>
                        <form action="/panel/financial/pay-subscribes" method="post" class="btn-block">
                            {{ csrf_field() }}
                            <input name="amount" value="{{ $subscribe->price }}" type="hidden">
                            <input name="id" value="{{ $subscribe->id }}" type="hidden">
                            <button type="submit" class="btn btn-primary btn-block mt-50">{{ trans('financial.purchase') }}</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/panel/financial/subscribes.min.js"></script>
@endpush
