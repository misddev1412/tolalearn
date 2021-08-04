

@if(!empty($meeting) and !empty($meeting->meetingTimes) and $meeting->meetingTimes->count() > 0)

    <div class="mt-40">
        <h3 class="font-16 font-weight-bold text-dark-blue">{{ trans('site.view_available_times') }}</h3>

        <div class="mt-35">
            <div class="row align-items-center justify-content-center">
                <input type="hidden" id="inlineCalender" class="form-control">
                <div class="inline-reservation-calender"></div>
            </div>
        </div>
    </div>

    <div class="mt-40 pick-a-time" id="PickTimeContainer" data-user-id="{{ $user["id"] }}">
        <div class="loading-img d-none text-center">
            <img src="/assets/default/img/loading.gif" width="80" height="80">
        </div>

        <form action="{{ (!$meeting->disabled) ? '/meetings/reserve' : '' }}" method="post" id="PickTimeBody" class="d-none">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="day" id="selectedDay" value="">

            <h3 class="font-16 font-weight-bold text-dark-blue">
                @if($meeting->disabled)
                    {{ trans('public.unavailable') }}
                @else
                    {{ trans('site.pick_a_time') }}
                    @if(!empty($meeting) and !empty($meeting->discount))
                        <span class="badge badge-danger text-white font-12">{{ $meeting->discount }}% {{ trans('public.off') }}</span>
                    @endif
                @endif
            </h3>

            <div class="d-flex flex-column mt-10">
                @if($meeting->disabled)
                    <span class="font-14 text-gray">{{ trans('public.unavailable_description') }}</span>
                @else
                    <span class="font-14 text-gray font-weight-500">
                    {{ trans('site.instructor_hourly_charge') }}
                        @if(!empty($meeting->discount))
                            <span class="text-decoration-line-through">{{ $currency }}{{ $meeting->amount }}</span>
                            <span class="text-primary">{{ $currency }}{{ $meeting->amount - (($meeting->amount * $meeting->discount) / 100) }}</span>
                        @else
                            <span class="text-primary">{{ $currency }}{{ $meeting->amount }}</span>
                        @endif
                </span>
                @endif

                <span class="font-14 text-gray mt-5 selected_date font-weight-500">{{ trans('site.selected_date') }}: <span></span></span>
            </div>

            <div id="availableTimes" class="d-flex flex-wrap align-items-center mt-25">

            </div>

            @if(!$meeting->disabled)
                <button type="submit" class="btn btn-sm btn-primary mt-30">{{ trans('meeting.reserve_appointment') }}</button>
            @endif
        </form>
    </div>
@else

    @include(getTemplate() . '.includes.no-result',[
       'file_name' => 'meet.png',
       'title' => trans('site.instructor_not_available'),
       'hint' => '',
    ])

@endif
