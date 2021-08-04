@extends('admin.layouts.app')

@push('styles_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{trans('admin/main.consultants_list_title')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/admin/">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{trans('admin/main.consultants')}}</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.total_consultants')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalConsultants }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-user-check"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.available_consultants')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $availableConsultants }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-user-times"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.unavailable_consultants')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $unavailableConsultants }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-users-slash"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.consultants_without_appointment')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $consultantsWithoutAppointment }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-body">

            <section class="card">
                <div class="card-body">
                    <form class="mb-0">
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
                                        <input type="date" id="fsdate" class="text-center form-control" name="from" value="{{ request()->get('from') }}" placeholder="Start Date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.end_date')}}</label>
                                    <div class="input-group">
                                        <input type="date" id="lsdate" class="text-center form-control" name="to" value="{{ request()->get('to') }}" placeholder="End Date">
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.filters')}}</label>
                                    <select name="sort" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{trans('admin/main.filter_type')}}</option>
                                        <option value="appointments_asc" @if(request()->get('sort') == 'appointments_asc') selected @endif>{{trans('admin/main.sales_appointments_ascending')}}</option>
                                        <option value="appointments_desc" @if(request()->get('sort') == 'appointments_desc') selected @endif>{{trans('admin/main.sales_appointments_descending')}}</option>
                                        <option value="appointments_income_asc" @if(request()->get('sort') == 'appointments_income_asc') selected @endif>{{trans('admin/main.appointments_income_ascending')}}</option>
                                        <option value="appointments_income_desc" @if(request()->get('sort') == 'appointments_income_desc') selected @endif>{{trans('admin/main.appointments_income_descending')}}</option>
                                        <option value="pending_appointments_asc" @if(request()->get('sort') == 'pending_appointments_asc') selected @endif>{{trans('admin/main.pending_appointments_ascending')}}</option>
                                        <option value="pending_appointments_desc" @if(request()->get('sort') == 'pending_appointments_desc') selected @endif>{{trans('admin/main.pending_appointments_descending')}}</option>
                                        <option value="created_at_asc" @if(request()->get('sort') == 'created_at_asc') selected @endif>{{trans('admin/main.register_date_ascending')}}</option>
                                        <option value="created_at_desc" @if(request()->get('sort') == 'created_at_desc') selected @endif>{{trans('admin/main.register_date_descending')}}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.organization')}}</label>
                                    <select name="organization_id" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{trans('admin/main.select_organization')}}</option>
                                        @foreach($organizations as $organization)
                                            <option value="{{ $organization->id }}" @if(request()->get('organization_id') == $organization->id) selected @endif>{{ $organization->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.users_group')}}</label>
                                    <select name="group_id" class="form-control populate">
                                        <option value="">{{trans('admin/main.select_users_group')}}</option>
                                        @foreach($userGroups as $userGroup)
                                            <option value="{{ $userGroup->id }}" @if(request()->get('group_id') == $userGroup->id) selected @endif>{{ $userGroup->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.status')}}</label>
                                    <select name="disabled" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{trans('admin/main.all_status')}}</option>
                                        <option value="0" @if(request()->get('disabled') == '0') selected @endif>{{trans('admin/main.available')}}</option>
                                        <option value="1" @if(request()->get('disabled') == '1') selected @endif>{{trans('admin/main.unavailable')}}</option>
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

            <div class="card">
                <div class="card-header">
                    @can('admin_consultants_export_excel')
                        <a href="/admin/consultants/excel?{{ http_build_query(request()->all()) }}" class="btn btn-primary">{{trans('admin/main.export_xls')}}</a>
                    @endcan

                    <div class="h-10"></div>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-center">
                        <table class="table table-striped font-14">
                            <tr>
                                <th>{{trans('admin/main.id')}}</th>
                                <th class="text-left">{{trans('admin/main.name')}}</th>
                                <th>{{trans('admin/main.appointments_sales')}}</th>
                                <th>{{trans('admin/main.pending_appointments')}}</th>
                                <th>{{trans('admin/main.wallet_charge')}}</th>
                                <th>{{trans('admin/main.user_group')}}</th>
                                <th>{{trans('admin/main.register_date')}}</th>
                                <th>{{trans('admin/main.status')}}</th>
                                <th width="120">{{trans('admin/main.actions')}}</th>

                            </tr>

                            @foreach($consultants as $consultant)
                                <tr>
                                    <td>{{ $consultant->id }}</td>

                                    <td class="text-left">
                                     <div class="d-flex align-items-center">
                                        <figure class="avatar mr-2">
                                            <img src="{{ $consultant->getAvatar() }}" alt="...">
                                        </figure>
                                        <div class="media-body ml-1">
                                            <div class="mt-0 mb-1 font-weight-bold">{{ $consultant->full_name }}</div>
                                            <div class="text-primary text-small font-600-bold">{{ $consultant->mobile }}</div>
                                        </div>
                                       </div>
                                    </td>

                                    <td>
                                        <div class="media-body">
                                            <div class="text-primary mt-0 mb-1 font-weight-bold">{{ $consultant->sales_count }}</div>

                                            @if($consultant->sales_amount > 0)
                                                <div class="text-small font-600-bold">{{ $currency }}{{ $consultant->sales_amount }}</div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        {{ $consultant->pendingAppointments }}
                                    </td>

                                    <td>
                                        @if($consultant->totalIncome > 0)
                                            {{ $currency }}{{ $consultant->getAccountingBalance() }}
                                        @else
                                            0
                                        @endif
                                    </td>

                                    <td>{{ !empty($consultant->userGroup) ? $consultant->userGroup->group->name : '-' }}</td>

                                    <td>{{ dateTimeFormat($consultant->created_at, 'Y/m/j - H:i') }}</td>

                                    <td>
                                        @if($consultant->disabled)
                                            <div class="text-danger mt-0 mb-1 font-weight-bold">{{trans('admin/main.unavailable')}}</div>
                                        @else
                                            <div class="text-success mt-0 mb-1 font-weight-bold">{{trans('admin/main.available')}}</div>
                                        @endif
                                    </td>

                                    <td class="text-center mb-2" width="120">
                                        @can('admin_users_impersonate')
                                            <a href="/admin/users/{{ $consultant->id }}/impersonate" target="_blank" class="btn-transparent  text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.login') }}">
                                                <i class="fa fa-user-shield"></i>
                                            </a>
                                        @endcan

                                        @can('admin_users_edit')
                                            <a href="/admin/users/{{ $consultant->id }}/edit" class="btn-transparent  text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcan

                                        @can('admin_users_delete')
                                            @include('admin.includes.delete_button',['url' => '/admin/users/'.$consultant->id.'/delete' , 'btnClass' => ''])
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach

                        </table>
                    </div>
                </div>

                <div class="card-footer text-center">
                    {{ $consultants->links() }}
                </div>
            </div>

            <section class="card">
                <div class="card-body">
           <div class="section-title ml-0 mt-0 mb-3"> <h4>{{trans('admin/main.hints')}}</h4> </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="media-body">
                                <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.consultants_hint_title_1')}}</div>
                                <div class=" text-small font-600-bold">{{trans('admin/main.consultants_hint_description_1')}}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="media-body">
                                <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.consultants_hint_title_2')}}</div>
                                <div class=" text-small font-600-bold">{{trans('admin/main.consultants_hint_description_2')}}</div>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="media-body">
                                <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.consultants_hint_title_3')}}</div>
                                <div class="text-small font-600-bold">{{trans('admin/main.consultants_hint_description_3')}}</div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </section>

@endsection

@push('scripts_bottom')
@endpush
