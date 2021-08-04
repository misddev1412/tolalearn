@extends('admin.layouts.app')

@push('styles_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.new_subscribe') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/admin/">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.subscribes') }}</div>
            </div>
        </div>


        <div class="section-body card">

            <div class="d-flex align-items-center justify-content-between">
                <div class="">
                    <h2 class="section-title ml-4">{{ !empty($subscribe) ? trans('admin/main.edit') : trans('admin/main.create') }}</h2>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-8 col-lg-6">
                        <div class="card-body">
                            <form action="/admin/financial/subscribes/{{ !empty($subscribe) ? $subscribe->id.'/update' : 'store' }}" method="Post">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label>{{ trans('admin/main.title') }}</label>
                                    <input type="text" name="title"
                                           class="form-control  @error('title') is-invalid @enderror"
                                           value="{{ !empty($subscribe) ? $subscribe->title : old('title') }}"/>
                                    @error('title')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>{{ trans('admin/main.short_description') }} ({{ trans('admin/main.optional') }})</label>
                                    <input type="text" name="description"
                                           class="form-control "
                                           value="{{ !empty($subscribe) ? $subscribe->description : old('description') }}"
                                           placeholder="{{ trans('admin/main.short_description_placeholder') }}"
                                    />
                                </div>


                                <div class="form-group">
                                    <label>{{ trans('admin/main.usable_count') }}</label>
                                    <input type="text" name="usable_count"
                                           class="form-control  @error('usable_count') is-invalid @enderror"
                                           value="{{ !empty($subscribe) ? $subscribe->usable_count : old('usable_count') }}"/>
                                    @error('usable_count')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>{{ trans('admin/main.days') }}</label>
                                    <input type="text" name="days"
                                           class="form-control  @error('days') is-invalid @enderror"
                                           value="{{ !empty($subscribe) ? $subscribe->days : old('days') }}"/>
                                    @error('days')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>


                                <div class="form-group">
                                    <label>{{ trans('admin/main.price') }}</label>
                                    <input type="text" name="price"
                                           class="form-control  @error('price') is-invalid @enderror"
                                           value="{{ !empty($subscribe) ? $subscribe->price : old('price') }}"/>
                                    @error('price')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mt-15">
                                    <label class="input-label">{{ trans('admin/main.icon') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button type="button" class="input-group-text admin-file-manager" data-input="icon" data-preview="holder">
                                                <i class="fa fa-chevron-up"></i>
                                            </button>
                                        </div>
                                        <input type="text" name="icon" id="icon" value="{{ !empty($subscribe->icon) ? $subscribe->icon : old('icon') }}" class="form-control @error('icon') is-invalid @enderror"/>
                                        @error('icon')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                        <div class="input-group-append">
                                            <button type="button" class="input-group-text admin-file-view" data-input="icon">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group custom-switches-stacked">
                                    <label class="custom-switch pl-0">
                                        <input type="hidden" name="is_popular" value="0">
                                        <input type="checkbox" name="is_popular" id="isPopular" value="1" {{ (!empty($subscribe) and $subscribe->is_popular) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                                        <span class="custom-switch-indicator"></span>
                                        <label class="custom-switch-description mb-0 cursor-pointer" for="isPopular">{{ trans('admin/pages/financial.is_popular') }}</label>
                                    </label>
                                </div>

                                <div class=" mt-4">
                                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                                </div>
                            </form>
                        </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')

@endpush
