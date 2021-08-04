@extends('admin.layouts.app')


@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.seo_metas') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/admin/">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item active"><a href="/admin/settings">{{ trans('admin/main.settings') }}</a></div>
                <div class="breadcrumb-item">{{ trans('admin/main.seo_metas') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <ul class="nav nav-pills" id="myTab3" role="tablist">
                                @foreach(\App\Models\Setting::$pagesSeoMetas as $page)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $loop->iteration == 1 ? 'active' : '' }}" id="{{ $page }}-tab" data-toggle="tab" href="#{{ $page }}" role="tab" aria-controls="{{ $page }}" aria-selected="true">{{ trans('admin/main.seo_metas_'.$page) }}</a>
                                    </li>
                                @endforeach
                            </ul>

                            @php
                                $itemValue = (!empty($settings) and !empty($settings['seo_metas'])) ? $settings['seo_metas']->value : '';
                            @endphp

                            <div class="tab-content" id="myTabContent2">
                                @foreach(\App\Models\Setting::$pagesSeoMetas as $page)
                                    <div class="tab-pane mt-3 fade {{ $loop->iteration == 1 ? 'show active' : '' }}" id="{{ $page }}" role="tabpanel" aria-labelledby="{{ $page }}-tab">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <form action="/admin/settings/seo_metas/store" method="post">
                                                    {{ csrf_field() }}

                                                    <div class="form-group">
                                                        <label>{{ trans('admin/main.title') }}</label>
                                                        <input type="text" name="value[{{ $page }}][title]" value="{{ (!empty($itemValue) and !empty($itemValue[$page])) ? $itemValue[$page]['title'] : old('title') }}" class="form-control  @error('title') is-invalid @enderror"/>
                                                        @error('title')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label>{{ trans('public.description') }}</label>
                                                        <textarea name="value[{{ $page }}][description]" rows="4" class="form-control  @error('description') is-invalid @enderror">{{ (!empty($itemValue) and !empty($itemValue[$page])) ? $itemValue[$page]['description'] : old('description') }}</textarea>
                                                        @error('description')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group custom-switches-stacked">
                                                        <label class="custom-switch pl-0 d-flex align-items-center">
                                                            <label class="custom-switch-description mb-0 mr-2">{{ trans('admin/main.no_index') }}</label>
                                                            <input type="hidden" name="value[{{ $page }}][robot]" value="noindex">
                                                            <input type="checkbox" name="value[{{ $page }}][robot]" id="{{ $page }}Robot" value="index" {{ (!empty($itemValue) and !empty($itemValue[$page]) and (empty($itemValue[$page]['robot']) or $itemValue[$page]['robot'] != 'noindex')) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                                                            <span class="custom-switch-indicator"></span>
                                                            <label class="custom-switch-description mb-0 cursor-pointer" for="{{ $page }}Robot">{{ trans('admin/main.index') }}</label>
                                                        </label>
                                                    </div>

                                                    <button type="submit" class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
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
            <div class="section-title ml-0 mt-0 mb-3"><h4>{{trans('admin/main.hints')}}</h4></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{ trans('admin/main.seo_metas_hint_title_1') }}</div>
                        <div class=" text-small font-600-bold mb-2">{{ trans('admin/main.seo_metas_hint_description_1') }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{ trans('admin/main.seo_metas_hint_title_2') }}</div>
                        <div class=" text-small font-600-bold mb-2">{{ trans('admin/main.seo_metas_hint_description_2') }}</div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
