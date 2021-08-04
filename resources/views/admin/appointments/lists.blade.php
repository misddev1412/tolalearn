@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/admin/">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-address-book"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.total_appointments')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalAppointments }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-user-clock"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.open_appointments')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $openAppointments }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.finished_appointments')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $finishedAppointments }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-users"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.total_reservatores')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalConsultants }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-body">
            <section class="card">
                <div class="card-body">
                    <form method="get" class="mb-0">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.search')}}</label>
                                    <input type="text" class="form-control" name="search" value="{{ request()->get('search') }}">
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.start_date')}}</label>
                                    <div class="input-group">
                                        <input type="date" id="from" class="text-center form-control" name="from" value="{{ request()->get('from') }}" placeholder="Start Date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.end_date')}}</label>
                                    <div class="input-group">
                                        <input type="date" id="to" class="text-center form-control" name="to" value="{{ request()->get('to') }}" placeholder="End Date">
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.status')}}</label>
                                    <select name="status" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{trans('admin/main.all_status')}}</option>
                                        <option value="{{ \App\Models\ReserveMeeting::$open }}" @if(request()->get('status') == \App\Models\ReserveMeeting::$open) selected @endif>Open</option>
                                        <option value="{{ \App\Models\ReserveMeeting::$finished }}" @if(request()->get('status') == \App\Models\ReserveMeeting::$finished) selected @endif>Finished</option>
                                        <option value="{{ \App\Models\ReserveMeeting::$canceled }}" @if(request()->get('status') == \App\Models\ReserveMeeting::$canceled) selected @endif>Canceled</option>
                                        <option value="{{ \App\Models\ReserveMeeting::$pending }}" @if(request()->get('status') == \App\Models\ReserveMeeting::$pending) selected @endif>Pending</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.filters')}}</label>
                                    <select name="sort" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{trans('admin/main.filter_type')}}</option>
                                        <option value="has_discount" @if(request()->get('sort') == 'has_discount') selected @endif>{{trans('admin/main.discounted_appointments')}}</option>
                                        <option value="free" @if(request()->get('sort') == 'free') selected @endif>{{trans('admin/main.free_appointments')}}</option>
                                        <option value="amount_asc" @if(request()->get('sort') == 'amount_asc') selected @endif>{{trans('admin/main.cost_ascending')}}</option>
                                        <option value="amount_desc" @if(request()->get('sort') == 'amount_desc') selected @endif>{{trans('admin/main.cost_descending')}}</option>
                                        <option value="date_asc" @if(request()->get('sort') == 'date_asc') selected @endif>{{trans('admin/main.date_ascending')}}</option>
                                        <option value="date_desc" @if(request()->get('sort') == 'date_desc') selected @endif>{{trans('admin/main.date_descending')}}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.consultant')}}</label>

                                    <select name="consultant_ids[]" multiple="multiple" data-search-option="consultants" class="form-control search-user-select2"
                                            data-placeholder="Search Consultants">

                                        @if(!empty($consultants) and $consultants->count() > 0)
                                            @foreach($consultants as $teacher)
                                                <option value="{{ $teacher->id }}" selected>{{ $teacher->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.reservatore')}}</label>

                                    <select name="user_ids[]" multiple="multiple" class="form-control search-user-select2"
                                            data-placeholder="Search Reservatores">

                                        @if(!empty($users) and $users->count() > 0)
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" selected>{{ $user->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group mt-1">
                                    <label class="input-label mb-4"> </label>
                                    <input type="submit" class="text-center btn btn-primary w-100" value="{{trans('admin/main.show_results')}}">
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </section>

            <section class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped text-center font-14">

                            <tr>
                                <th class="text-left">{{trans('admin/main.consultant')}}</th>
                                <th class="text-left">{{trans('admin/main.reservatore')}}</th>
                                <th class="text-center">{{trans('admin/main.cost')}}</th>
                                <th class="text-center">{{trans('admin/main.time')}}</th>
                                <th class="text-center">{{trans('admin/main.date')}}</th>
                                <th class="text-center">{{trans('admin/main.status')}}</th>
                                <th class="text-center">{{trans('admin/main.actions')}}</th>
                            </tr>

                            @foreach($appointments as $appointment)
                                <tr>
                                    <td class="text-left">
                                        <a href="{{ $appointment->meeting->creator->getProfileUrl() }}" target="_blank">{{ $appointment->meeting->creator->full_name }}</a>
                                    </td>

                                    <td class="text-left">
                                        <a href="{{ $appointment->user->getProfileUrl() }}" target="_blank">{{ $appointment->user->full_name }}</a>
                                    </td>

                                    <td>
                                        <div class="media-body">
                                            <div class=" mt-0 mb-1 font-weight-bold">{{ $currency }}{{ $appointment->paid_amount }}</div>
                                        </div>
                                    </td>

                                    <td class="text-center">{{ $appointment->meetingTime->time }}</td>

                                    <td class="text-center">{{ dateTimeFormat(strtotime($appointment->day), 'd M Y') }}</td>

                                    <td class="text-center">
                                        @switch($appointment->status)
                                            @case(\App\Models\ReserveMeeting::$pending)
                                            <span class="text-primary">{{ trans('public.pending') }}</span>
                                            @break
                                            @case(\App\Models\ReserveMeeting::$open)
                                            <span class="text-warning">{{ trans('public.open') }}</span>
                                            @break
                                            @case(\App\Models\ReserveMeeting::$finished)
                                            <span class="text-success">{{ trans('public.finished') }}</span>
                                            @break
                                            @case(\App\Models\ReserveMeeting::$canceled)
                                            <span class="text-danger">{{ trans('public.canceled') }}</span>
                                            @break
                                        @endswitch
                                    </td>

                                    <td class="text-center" width="50">
                                        <input type="hidden" class="js-meeting-password" value="{{ $appointment->password }}">
                                        <input type="hidden" class="js-meeting-link" value="{{ $appointment->link }}">

                                        @can('admin_appointments_join')
                                            @if(!empty($appointment->link) and $appointment->status == \App\Models\ReserveMeeting::$open)
                                                <button type="button" data-reserve-id="{{ $appointment->id }}" class="js-show-join-modal btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{trans('admin/main.join_link')}}"><i class="fa fa-link" aria-hidden="true"></i></button>
                                            @endif
                                        @endcan

                                        @can('admin_appointments_send_reminder')
                                            <button type="button" data-reserve-id="{{ $appointment->id }}" class="js-send-reminder btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{trans('admin/main.send_appointment_reminder')}}"><i class="fa fa-bell" aria-hidden="true"></i></button>
                                        @endcan

                                        @can('admin_appointments_cancel')
                                            @if($appointment->status != \App\Models\ReserveMeeting::$canceled)
                                                @include('admin.includes.delete_button',['url' => '/admin/appointments/'.$appointment->id.'/cancel'])
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach

                        </table>
                    </div>
                </div>

                <div class="card-footer text-center">
                    {{ $appointments->links() }}
                </div>
            </section>
        </div>
    </section>


     <section class="card">
        <div class="card-body">
           <div class="section-title ml-0 mt-0 mb-3"> <h5>{{trans('admin/main.hints')}}</h5> </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.appointments_hint_title_1')}}</div>
                        <div class=" text-small font-600-bold">{{trans('admin/main.appointments_hint_description_1')}}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.appointments_hint_title_2')}}</div>
                        <div class=" text-small font-600-bold">{{trans('admin/main.appointments_hint_description_2')}}</div>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.appointments_hint_title_3')}}</div>
                        <div class="text-small font-600-bold">{{trans('admin/main.appointments_hint_description_3')}}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="joinModal" tabindex="-1" aria-labelledby="contactMessageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactMessageLabel">{{trans('admin/main.join_appointment')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <div class="form-group">
                                <label class="input-label">{{trans('admin/main.url')}}</label>
                                <input type="text" name="link" class="form-control" disabled/>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="input-label">{{trans('admin/main.password')}}</label>
                                <input type="text" name="password" class="form-control" disabled/>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="" target="_blank" class="js-join-btn btn btn-primary">{{trans('admin/main.join')}}</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('admin/main.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sendReminderModal" tabindex="-1" aria-labelledby="contactMessageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactMessageLabel">{{trans('admin/main.send_reminder')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="">
                        <strong>{{trans('admin/main.consultant')}}:</strong>
                        <span class="js-consultant"></span>
                    </div>

                    <div class="mt-2">
                        <strong>{{trans('admin/main.reservatore')}}:</strong>
                        <span class="js-reservatore"></span>
                    </div>

                    <div class="mt-2">
                        <strong>{{trans('admin/main.remind_title')}}:</strong>
                        <span class="js-title"></span>
                    </div>

                    <div class="mt-2">
                        <strong>{{trans('admin/main.remind_message')}}:</strong>
                        <span class="js-message"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="" class="js-send-reminder-btn btn btn-primary">{{trans('admin/main.send')}}</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('admin/main.close') }}</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts_bottom')
@endpush
