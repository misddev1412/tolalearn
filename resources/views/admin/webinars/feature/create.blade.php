@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.feature_webinar_create') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/admin/">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.feature_webinar_create') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @php
                                $pages = \App\Models\FeatureWebinar::$pages;
                            @endphp

                            <ul class="nav nav-pills" id="myTab3" role="tablist">
                                @foreach($pages as  $page)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $loop->iteration == 1 ? ' active' : '' }}" id="{{ $page }}-tab" data-toggle="tab" href="#{{ $page }}" role="tab" aria-controls="{{ $page }}" aria-selected="true">{{ trans('admin/main.page_'.$page) }}</a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content" id="myTabContent2">

                                @foreach($pages as $page)
                                    <div class="tab-pane mt-3 fade {{ $loop->iteration == 1 ? ' active show' : '' }}" id="{{ $page }}" role="tabpanel" aria-labelledby="{{ $page }}-tab">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <form action="/admin/webinars/features/store" method="post">
                                                    {{ csrf_field() }}

                                                    <input type="hidden" name="page" value="{{ $page }}">

                                                    <div class="form-group">
                                                        <label class="input-label d-block">{{ trans('admin/main.webinar') }}</label>
                                                        <select name="webinar_id" class="form-control search-webinar-select2 @error('webinar_id') is-invalid @enderror" data-placeholder="{{ trans('admin/main.search_webinar') }}">

                                                        </select>

                                                        @error('webinar_id')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="input-label d-block">{{ trans('public.description') }}</label>
                                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="6"></textarea>

                                                        @error('description')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="input-label">{{ trans('admin/main.status') }}</label>
                                                        <select class="custom-select" name="status">
                                                            <option selected disabled>{{ trans('admin/main.status') }}</option>
                                                            <option value="pending">{{ trans('admin/main.pending') }}</option>
                                                            <option value="publish">{{ trans('admin/main.published') }}</option>
                                                        </select>
                                                    </div>

                                                    <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="section-title ml-0 mt-0 mb-3"><h5>{{trans('admin/main.hints')}}</h5></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.new_featured_hint_title_1')}}</div>
                        <div class=" text-small font-600-bold mb-2">{{trans('admin/main.new_featured_hint_description_1')}}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.new_featured_hint_title_2')}}</div>
                        <div class=" text-small font-600-bold mb-2">{{trans('admin/main.new_featured_hint_description_2')}}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts_bottom')

@endpush
