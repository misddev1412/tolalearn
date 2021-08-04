@extends('admin.layouts.app')

@push('libraries_top')

@endpush

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


        <div class="section-body">

            <section class="card">
                <div class="card-body">
                    <form class="mb-0">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.search')}}</label>
                                    <input type="text" class="form-control" name="search" value="{{ request()->get('search') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.start_date')}}</label>
                                    <div class="input-group">
                                        <input type="date" id="fsdate" class="text-center form-control" name="from" value="{{ request()->get('from') }}" placeholder="Start Date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.end_date')}}</label>
                                    <div class="input-group">
                                        <input type="date" id="lsdate" class="text-center form-control" name="to" value="{{ request()->get('to') }}" placeholder="End Date">
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.sender')}}</label>
                                    <select name="sender" data-plugin-selectTwo class="form-control populate">
                                        <option value="">Select Sender</option>
                                        <option value="admin" @if(request()->get('sender') == 'admin') selected @endif>{{trans('admin/main.admin_role')}}</option>
                                        <option value="organizations" @if(request()->get('sender') == 'organizations') selected @endif>{{trans('admin/main.organizations')}}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.types')}}</label>
                                    <select name="type" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{trans('admin/main.all_types')}}</option>

                                        @foreach(\App\Models\Noticeboard::$adminTypes as $type)
                                            <option value="{{ $type }}" @if(request()->get('type') == $type) selected @endif>{{ trans('public.'.$type) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-4">
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
                    @can('admin_noticeboards_send')
                        <div class="text-right">
                            <a href="/admin/noticeboards/send" class="btn btn-primary">{{trans('admin/main.send_noticeboard')}}</a>
                        </div>
                    @endcan
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped font-14" id="datatable-basic">

                            <tr>
                                <th class="text-left">{{ trans('admin/main.title') }}</th>
                                <th class="text-center">{{ trans('notification.sender') }}</th>
                                <th class="text-center">{{ trans('site.message') }}</th>
                                <th class="text-center">{{ trans('admin/main.type') }}</th>
                                <th class="text-center">{{ trans('admin/main.created_at') }}</th>
                                <th>{{ trans('admin/main.actions') }}</th>
                            </tr>

                            @foreach($noticeboards as $noticeboard)
                                <tr>
                                    <td class="text-left">{{ $noticeboard->title }}</td>
                                    <td class="text-center">{{ $noticeboard->sender }}</td>

                                    <td class="text-center">
                                        <button type="button" data-item-id="{{ $noticeboard->id }}" class="js-show-description btn btn-outline-primary">{{ trans('admin/main.show') }}</button>
                                        <input type="hidden" value="{{ nl2br($noticeboard->message) }}">
                                    </td>
                                    <td class="text-center">{{ trans('admin/main.notification_'.$noticeboard->type) }}</td>

                                    <td class="text-center">{{ dateTimeFormat($noticeboard->created_at,'Y M j | H:i') }}</td>

                                    <td width="100">
                                        @can('admin_noticeboards_edit')
                                            <a href="/admin/noticeboards/{{ $noticeboard->id }}/edit" class="btn-transparent  text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcan

                                        @can('admin_notifications_delete')
                                            @include('admin.includes.delete_button',['url' => '/admin/noticeboards/'. $noticeboard->id.'/delete','btnClass' => ''])
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach

                        </table>
                    </div>
                </div>

                <div class="card-footer text-center">
                    {{ $noticeboards->links() }}
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="notificationMessageModal" tabindex="-1" aria-labelledby="notificationMessageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationMessageLabel">{{ trans('admin/main.contacts_message') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('admin/main.close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')
@endpush
