@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')
    <section>
        <h2 class="section-title">{{ trans('financial.account_summary') }}</h2>

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
    @if (\Session::has('msg'))
        <div class="alert alert-warning">
            <ul>
                <li>{{ \Session::get('msg') }}</li>
            </ul>
        </div>
    @endif

    @php
        $showOfflineFields = false;
        if ($errors->has('date') or $errors->has('referral_code') or $errors->has('account') or !empty($editOfflinePayment)) {
            $showOfflineFields = true;
        }
    @endphp

    <section class="mt-30">
        <h2 class="section-title">{{ trans('financial.select_the_payment_gateway') }}</h2>

        <form action="/panel/financial/{{ !empty($editOfflinePayment) ? 'offline-payments/'. $editOfflinePayment->id .'/update' : 'charge' }}" method="post" class="mt-25">
            {{csrf_field()}}

            @if($errors->has('gateway'))
                <div class="text-danger mb-3">{{ $errors->first('gateway') }}</div>
            @endif

            <div class="row">
                @foreach($paymentChannels as $paymentChannel)
                    <div class="col-6 col-lg-3 mb-40 charge-account-radio">
                        <input type="radio" class="online-gateway" name="gateway" id="{{ $paymentChannel->class_name }}" @if(old('gateway') == $paymentChannel->class_name) checked @endif value="{{ $paymentChannel->class_name }}">
                        <label for="{{ $paymentChannel->class_name }}" class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center">
                            <img src="{{ $paymentChannel->image }}" width="120" height="60" alt="">
                            <p class="mt-30 font-14 font-weight-500 text-dark-blue">{{ trans('financial.pay_via') }}
                                <span class="font-weight-bold">{{ $paymentChannel->title }}</span>
                            </p>
                        </label>
                    </div>
                @endforeach

                <div class="col-6 col-lg-3 mb-40 charge-account-radio">
                    <input type="radio" name="gateway" id="offline" value="offline" @if(old('gateway') == 'offline' or !empty($editOfflinePayment)) checked @endif>
                    <label for="offline" class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center">
                        <img src="/assets/default/img/activity/pay.svg" width="120" height="60" alt="">
                        <p class="mt-30 font-14 font-weight-500 text-dark-blue">{{ trans('financial.pay_via') }}
                            <span class="font-weight-bold">{{ trans('financial.offline') }}</span>
                        </p>
                    </label>
                </div>
            </div>

            <div class="">
                <h3 class="section-title mb-20">{{ trans('financial.finalize_payment') }}</h3>

                <div class="row">
                    <div class="col-12 col-lg-3 mb-25 mb-lg-0">
                        <label class="font-weight-500 font-14 text-dark-blue d-block">{{ trans('panel.amount') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text text-white font-16">
                                    {{--<i data-feather="dollar-sign" width="18" height="18" class="text-white"></i>--}}
                                    {{ $currency }}
                                </span>
                            </div>
                            <input type="text" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ !empty($editOfflinePayment) ? $editOfflinePayment->amount : old('amount') }}"
                                   placeholder="{{ trans('panel.number_only') }}"/>
                            <div class="invalid-feedback">@error('amount') {{ $message }} @enderror</div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-3 mb-25 mb-lg-0 js-offline-payment-input " style="{{ (!$showOfflineFields) ? 'display:none' : '' }}">
                        <div class="form-group">
                            <label class="input-label">{{ trans('financial.account') }}</label>
                            <select name="account" class="form-control @error('account') is-invalid @enderror">
                                <option selected disabled>{{ trans('financial.select_the_account') }}</option>
                                @if(!empty(getOfflineBanksTitle()) and count(getOfflineBanksTitle())) {
                                @foreach(getOfflineBanksTitle() as $accountType)
                                    <option value="{{ $accountType }}" @if(!empty($editOfflinePayment) and $editOfflinePayment->bank == $accountType) selected @endif>{{ $accountType }}</option>
                                @endforeach
                                @endif
                            </select>

                            @error('account')
                            <div class="invalid-feedback"> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 col-lg-3 mb-25 mb-lg-0 js-offline-payment-input " style="{{ (!$showOfflineFields) ? 'display:none' : '' }}">
                        <div class="form-group">
                            <label for="referralCode" class="input-label">{{ trans('financial.referral_code') }}</label>
                            <input type="text" name="referral_code" id="referralCode" value="{{ !empty($editOfflinePayment) ? $editOfflinePayment->reference_number : old('referral_code') }}" class="form-control @error('referral_code') is-invalid @enderror"/>
                            @error('referral_code')
                            <div class="invalid-feedback"> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 col-lg-3 mb-25 mb-lg-0 js-offline-payment-input " style="{{ (!$showOfflineFields) ? 'display:none' : '' }}">
                        <div class="form-group">
                            <label class="input-label">{{ trans('public.date_time') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="dateRangeLabel">
                                        <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                                    </span>
                                </div>
                                <input type="text" name="date" value="{{ !empty($editOfflinePayment) ? $editOfflinePayment->pay_date : old('date') }}" class="form-control datetimepicker @error('date') is-invalid @enderror"
                                       aria-describedby="dateRangeLabel"/>
                                @error('date')
                                <div class="invalid-feedback"> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-3">
                        <div class="mt-30">
                            <button type="button" id="submitChargeAccountForm" class="btn btn-primary btn-sm">{{ trans('public.pay') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>

    <section class="mt-40">
        <h2 class="section-title">{{ trans('financial.bank_accounts_information') }}</h2>

        <div class="row mt-25">
            @if(!empty($siteBankAccounts))
                @foreach($siteBankAccounts as $siteBankAccount)
                    <div class="col-12 col-lg-3 mb-30 mb-lg-0">
                        <div class="py-25 px-20 rounded-sm panel-shadow d-flex flex-column align-items-center justify-content-center">
                            <img src="{{ $siteBankAccount['image'] }}" width="120" height="60" alt="">

                            <div class="mt-15 mt-30 w-100">

                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="font-14 font-weight-500 text-secondary">{{ trans('public.name') }}:</span>
                                    <span class="font-14 font-weight-500 text-gray">{{ $siteBankAccount['title'] }}</span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mt-10">
                                    <span class="font-14 font-weight-500 text-secondary">{{ trans('financial.card_id') }}:</span>
                                    <span class="font-14 font-weight-500 text-gray">{{ $siteBankAccount['card_id'] }}</span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mt-10">
                                    <span class="font-14 font-weight-500 text-secondary">{{ trans('financial.account_id') }}:</span>
                                    <span class="font-14 font-weight-500 text-gray">{{ $siteBankAccount['account_id'] }}</span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mt-10">
                                    <span class="font-14 font-weight-500 text-secondary">{{ trans('financial.iban') }}:</span>
                                    <span class="font-14 font-weight-500 text-gray">{{ $siteBankAccount['iban'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    @if($offlinePayments->count() > 0)
        <section class="mt-40">
            <h2 class="section-title">{{ trans('financial.offline_transactions_history') }}</h2>

            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table text-center custom-table">
                                <thead>
                                <tr>
                                    <th>{{ trans('financial.bank') }}</th>
                                    <th>{{ trans('financial.referral_code') }}</th>
                                    <th class="text-center">{{ trans('panel.amount') }} ({{ $currency }})</th>
                                    <th class="text-center">{{ trans('public.status') }}</th>
                                    <th class="text-right">{{ trans('public.controls') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($offlinePayments as $offlinePayment)
                                    <tr>
                                        <td class="text-left">
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-500 text-dark-blue">{{ $offlinePayment->bank }}</span>
                                                <span class="font-12 text-gray">{{ $offlinePayment->pay_date }}</span>
                                            </div>
                                        </td>
                                        <td class="text-left align-middle">
                                            <span>{{ $offlinePayment->reference_number }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="font-16 font-weight-bold text-primary">{{ $offlinePayment->amount }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            @switch($offlinePayment->status)
                                                @case(\App\Models\OfflinePayment::$waiting)
                                                <span class="text-primary">{{ trans('public.waiting') }}</span>
                                                @break
                                                @case(\App\Models\OfflinePayment::$approved)
                                                <span class="text-primary">{{ trans('financial.approved') }}</span>
                                                @case(\App\Models\OfflinePayment::$reject)
                                                @break
                                                <span class="text-primary">{{ trans('public.rejected') }}</span>
                                                @break
                                            @endswitch
                                        </td>
                                        <td class="text-right align-middle">
                                            @if($offlinePayment->status != 'approved')
                                                <div class="btn-group dropdown table-actions">
                                                    <button type="button" class="btn-transparent dropdown-toggle"
                                                            data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                        <i data-feather="more-vertical" height="20"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a href="/panel/financial/offline-payments/{{ $offlinePayment->id }}/edit"
                                                           class="webinar-actions d-block mt-10">{{ trans('public.edit') }}</a>
                                                        <a href="/panel/financial/offline-payments/{{ $offlinePayment->id }}/delete" data-item-id="1"
                                                           class="webinar-actions d-block mt-10 delete-action">{{ trans('public.delete') }}</a>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </section>

    @else

        @include(getTemplate() . '.includes.no-result',[
            'file_name' => 'offline.png',
            'title' => trans('financial.offline_no_result'),
            'hint' => nl2br(trans('financial.offline_no_result_hint')),
        ])

    @endif
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>

    <script src="/assets/default/js/panel/financial/account.min.js"></script>

    <script>
        (function ($) {
            "use strict";
            @if(session()->has('sweetalert'))
            Swal.fire({
                icon: "{{ session()->get('sweetalert')['status'] ?? 'success' }}",
                html: '<h3 class="font-20 text-center text-dark-blue py-25">{{ session()->get('sweetalert')['msg'] ?? '' }}</h3>',
                showConfirmButton: false,
                width: '25rem',
            });
            @endif
        })(jQuery)
    </script>
@endpush
