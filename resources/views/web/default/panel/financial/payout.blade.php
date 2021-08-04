@extends(getTemplate() .'.panel.layouts.panel_layout')

@section('content')
    <section>
        <h2 class="section-title">{{ trans('financial.account_summary') }}</h2>

        @if(!$authUser->financial_approval)
            <div class="p-15 mt-30 p-lg-25 not-verified-alert font-weight-500 text-dark-blue rounded-sm shadow">
                {{ trans('panel.not_verified_alert') }}
                <a href="/panel/setting" class="text-decoration-underline">{{ trans('panel.this_link') }}</a>.
            </div>
        @endif

        <div class="activities-container mt-25 p-20 p-lg-35">
            <div class="row">
                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/36.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $currency }}{{ $accountCharge ?? 0 }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('financial.account_charge') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/37.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $currency }}{{ $readyPayout ?? 0 }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('financial.ready_to_payout') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/38.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $currency }}{{ $totalIncome ?? 0 }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('financial.total_income') }}</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <div class="mt-45">
        <button type="button" @if(!$authUser->financial_approval) disabled @endif class="request-payout btn btn-sm btn-primary">{{ trans('financial.request_payout') }}</button>
    </div>

    @if($payouts->count() > 0)
        <section class="mt-35">
            <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
                <h2 class="section-title">{{ trans('financial.payouts_history') }}</h2>
            </div>

            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table text-center custom-table">
                                <thead>
                                <tr>
                                    <th>{{ trans('financial.account') }}</th>
                                    <th class="text-center">{{ trans('public.type') }}</th>
                                    <th class="text-center">{{ trans('panel.amount') }} ({{ $currency }})</th>
                                    <th class="text-center">{{ trans('public.status') }}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($payouts as $payout)
                                    <tr>
                                        <td>
                                            <div class="text-left">
                                                <span class="d-block font-weight-500 text-dark-blue">{{ $payout->account_name }}</span>
                                                <span class="d-block font-12 text-gray mt-1">{{ dateTimeFormat($payout->created_at, 'Y M j | H:i') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span>{{ trans('public.manual') }}</span>
                                        </td>
                                        <td>
                                            <span class="text-primary font-weight-bold">{{ $payout->amount }}</span>
                                        </td>
                                        <td>
                                            @switch($payout->status)
                                                @case(\App\Models\Payout::$waiting)
                                                <span class="text-warning font-weight-bold">{{ trans('public.waiting') }}</span>
                                                @break;
                                                @case(\App\Models\Payout::$reject)
                                                <span class="text-danger font-weight-bold">{{ trans('public.rejected') }}</span>
                                                @break;
                                                @case(\App\Models\Payout::$done)
                                                <span class="">{{ trans('public.done') }}</span>
                                                @break;
                                            @endswitch
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="my-30">
                {{ $payouts->links('vendor.pagination.panel') }}
            </div>
        </section>
    @else
        @include(getTemplate() . '.includes.no-result',[
            'file_name' => 'payout.png',
            'title' => trans('financial.payout_no_result'),
            'hint' => nl2br(trans('financial.payout_no_result_hint')),
        ])

    @endif


    <div id="requestPayoutModal" class="d-none">
        <h3 class="section-title after-line font-20 text-dark-blue mb-25">{{ trans('financial.payout_confirmation') }}</h3>
        <p class="text-gray mt-15">{{ trans('financial.payout_confirmation_hint') }}</p>
        <form method="post" action="/panel/financial/request-payout">
            {{ csrf_field() }}
            <div class="row justify-content-center">
                <div class="w-75 mt-50">
                    <div class="d-flex align-items-center justify-content-between text-gray">
                        <span class="font-weight-bold">{{ trans('financial.ready_to_payout') }}</span>
                        <span>{{ $currency }}{{ $readyPayout ?? 0 }}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between text-gray mt-20">
                        <span class="font-weight-bold">{{ trans('financial.account_type') }}</span>
                        <span>{{ $authUser->account_type }}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between text-gray mt-20">
                        <span class="font-weight-bold">{{ trans('financial.account_id') }}</span>
                        <span>{{ $authUser->account_id }}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between text-gray mt-20">
                        <span class="font-weight-bold">{{ trans('financial.iban') }}</span>
                        <span>{{ $authUser->iban }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-50 d-flex align-items-center justify-content-end">
                <button type="button" class="js-submit-payout btn btn-sm btn-primary">{{ trans('financial.request_payout') }}</button>
                <button type="button" class="btn btn-sm btn-danger ml-10 close-swl">{{ trans('public.close') }}</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/panel/financial/payout.min.js"></script>
@endpush
