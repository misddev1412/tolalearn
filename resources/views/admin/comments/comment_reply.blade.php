@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.reply_comment') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/admin/">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.reply_comment') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header flex-column align-items-start">
                            <h4>{{ trans('admin/main.main_comment') }}</h4>
                            <p class="mt-2">{{ nl2br($comment->comment) }}</p>

                            <hr class="divider my-2 w-100 border border-gray">

                            @if(!empty($comment->replies) and $comment->replies->count() > 0)
                                <div class="mt-1 w-100">
                                    <h4>{{ trans('admin/main.reply_list') }}</h4>

                                    <div class="table-responsive">
                                        <table class="table table-striped font-14">
                                            <tr>
                                                <th>{{ trans('admin/main.user') }}</th>
                                                <th>{{ trans('admin/main.comment') }}</th>
                                                <th>{{ trans('public.date') }}</th>
                                                <th>{{ trans('admin/main.status') }}</th>
                                                <th>{{ trans('admin/main.type') }}</th>
                                                <th>{{ trans('admin/main.action') }}</th>
                                            </tr>
                                            @foreach($comment->replies as $reply)
                                                <tr>
                                                    <td>{{ $reply->user->id .' - '.$reply->user->full_name }}</td>
                                                    <td width="30%">{{ nl2br($reply->comment) }}</td>
                                                    <td>{{ dateTimeFormat($reply->created_at, 'Y M j | H:i') }}</td>
                                                    <td>
                                                        <span class="text-{{ ($reply->status == 'pending') ? 'warning' : 'success' }}">
                                                            {{ ($reply->status == 'pending') ? trans('admin/main.pending') : trans('admin/main.published') }}
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <span class="text-{{ (empty($reply->reply_id)) ? 'info' : 'warning' }}">
                                                            {{ (empty($reply->reply_id)) ? trans('admin/main.main_comment') : trans('admin/main.replied') }}
                                                        </span>
                                                    </td>

                                                    <td>

                                                        @can('admin_'. $itemRelation .'_comments_status')
                                                            <a href="/admin/{{ $page }}/comments/{{ $reply->id }}/toggle" class="btn btn-{{ (($reply->status == 'pending') ? 'success' : 'primary') }} btn-sm">{{ trans('admin/main.'.(($reply->status == 'pending') ? 'publish' : 'pending')) }}</a>
                                                        @endcan

                                                        @can('admin_'. $itemRelation .'_comments_edit')
                                                            <a href="/admin/{{ $page }}/comments/{{ $reply->id }}/edit" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        @endcan

                                                        <br>

                                                        @can('admin_'. $itemRelation .'_comments_delete')
                                                            @include('admin.includes.delete_button',['url' => '/admin/'. $page .'/comments/'.$reply->id.'/delete','btnClass' => 'btn-sm mt-2'])
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="card-body ">
                            <form action="/admin/{{ $page }}/comments/{{ $comment->id }}/reply" method="post">
                                {{ csrf_field() }}

                                <div class="form-group mt-15">
                                    <label class="input-label">{{ trans('admin/main.reply_comment') }}</label>
                                    <textarea id="summernote" name="comment" class="summernote form-control @error('comment')  is-invalid @enderror">{{ old('comment')  }}</textarea>

                                    @error('comment')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <button type="submit" class="mt-3 btn btn-primary">{{ trans('admin/main.save_change') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
@endpush
