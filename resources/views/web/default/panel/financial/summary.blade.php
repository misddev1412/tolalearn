@extends(getTemplate() .'.panel.layouts.panel_layout')


@section('content')
    @if($accountings->count() > 0)
        <section>
            <h2 class="section-title">{{ trans('financial.financial_documents') }}</h2>

            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table text-center custom-table">
                                <thead>
                                <tr>
                                    <th>{{ trans('public.title') }}</th>
                                    <th>{{ trans('public.description') }}</th>
                                    <th class="text-center">{{ trans('panel.amount') }} ({{ $currency }})</th>
                                    <th class="text-center">{{ trans('public.creator') }}</th>
                                    <th class="text-center">{{ trans('public.date') }}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($accountings as $accounting)
                                    <tr>
                                        <td class="text-left">
                                            <div class="d-flex flex-column">
                                            <span class="font-14 font-weight-500">
                                                @if(!empty($accounting->webinar_id))
                                                    {{ $accounting->webinar->title }}
                                                @elseif(!empty($accounting->meeting_time_id))
                                                    {{ trans('meeting.reservation_appointment') }}
                                                @elseif(!empty($accounting->subscribe_id) and !empty($accounting->subscribe))
                                                    {{ $accounting->subscribe->title }}
                                                @elseif(!empty($accounting->promotion_id) and !empty($accounting->promotion))
                                                    {{ $accounting->promotion->title }}
                                                @elseif($accounting->store_type == \App\Models\Accounting::$storeManual)
                                                    {{ trans('financial.manual_document') }}
                                                @elseif($accounting->type == \App\Models\Accounting::$addiction and $accounting->type_account == \App\Models\Accounting::$asset)
                                                    {{ trans('financial.charge_account') }}
                                                @elseif($accounting->type == \App\Models\Accounting::$deduction and $accounting->type_account == \App\Models\Accounting::$income)
                                                    {{ trans('financial.payout') }}
                                                @else
                                                    ---
                                                @endif
                                            </span>

                                                <span class="font-12 text-gray">
                                                @if(!empty($accounting->webinar_id))
                                                        {{ $accounting->webinar->id }}
                                                    @elseif(!empty($accounting->meeting_time_id))
                                                        {{ $accounting->meetingTime->meeting->creator->full_name }}
                                                    @elseif(!empty($accounting->subscribe_id) and !empty($accounting->subscribe))
                                                        {{ $accounting->subscribe->id }}
                                                    @elseif(!empty($accounting->promotion_id) and !empty($accounting->promotion))
                                                        {{ $accounting->promotion->id }}
                                                    @else
                                                        ---
                                                    @endif
                                            </span>
                                            </div>
                                        </td>
                                        <td class="text-left align-middle">
                                            <span class="font-weight-500 text-gray">{{ $accounting->description }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            @switch($accounting->type)
                                                @case(\App\Models\Accounting::$addiction)
                                                <span class="font-16 font-weight-bold text-primary">+{{ $accounting->amount }}</span>
                                                @break;
                                                @case(\App\Models\Accounting::$deduction)
                                                <span class="font-16 font-weight-bold text-danger">-{{ $accounting->amount }}</span>
                                                @break;
                                            @endswitch
                                        </td>
                                        <td class="text-center align-middle">{{ trans('public.'.$accounting->store_type) }}</td>
                                        <td class="text-center align-middle">
                                            <span>{{ dateTimeFormat($accounting->created_at, 'j F Y') }}</span>
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
            'file_name' => 'financial.png',
            'title' => trans('financial.financial_summary_no_result'),
            'hint' => nl2br(trans('financial.financial_summary_no_result_hint')),
        ])
    @endif
    <div class="my-30">
        {{ $accountings->links('vendor.pagination.panel') }}
    </div>
@endsection
