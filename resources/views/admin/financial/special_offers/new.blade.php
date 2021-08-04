@extends('admin.layouts.app')


@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.special_offers') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/admin/">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.special_offers') }}</div>
            </div>
        </div>


        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-8 col-lg-6">
                            <form action="/admin/financial/special_offers/{{ !empty($specialOffer) ? $specialOffer->id.'/update' : 'store' }}"
                                  method="Post">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label>{{ trans('admin/main.name') }}</label>
                                    <input type="text" name="name"
                                           class="form-control  @error('name') is-invalid @enderror"
                                           value="{{ !empty($specialOffer) ? $specialOffer->name : old('name') }}"
                                           placeholder="{{ trans('admin/main.name_placeholder') }}"/>
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>{{ trans('admin/main.class') }}</label>

                                    <select name="webinar_id" class="form-control search-webinar-select2 @error('webinar_id')  is-invalid @enderror"
                                            data-placeholder="Search and Select Class">

                                        @if(!empty($specialOffer))
                                            <option value="{{ $specialOffer->webinar->id }}" selected>{{ $specialOffer->webinar->title }}</option>
                                        @endif
                                    </select>
                                    @error('webinar_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label>{{ trans('admin/main.discount_percentage') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-percentage"></i>
                                            </div>
                                        </div>
                                        <input type="number"
                                               name="percent" class="spinner-input form-control text-center  @error('percent') is-invalid @enderror"
                                               maxlength="3" min="0" max="100">
                                        @error('percent')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.from_date') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="dateRangeLabel">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="from_date" class="form-control text-center datetimepicker"
                                               aria-describedby="dateRangeLabel"
                                               value="{{ !empty($specialOffer) ? dateTimeFormat($specialOffer->from_date,'Y-m-d H:i') : old('from_date') }}"/>
                                        @error('from_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.to_date') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="dateRangeLabel">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="to_date" class="form-control text-center datetimepicker"
                                               aria-describedby="dateRangeLabel"
                                               value="{{ !empty($specialOffer) ? dateTimeFormat($specialOffer->to_date,'Y-m-d H:i') : old('to_date') }}"/>
                                        @error('to_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ trans('admin/main.status') }}</label>
                                    <select name="status" class="form-control custom-select @error('status')  is-invalid @enderror">
                                        <option value="active" {{ !empty($specialOffer) and $specialOffer->status == \App\Models\SpecialOffer::$active ? 'selected' : '' }}>{{ trans('panel.active') }}</option>
                                        <option value="inactive" {{ !empty($specialOffer) and $specialOffer->status == \App\Models\SpecialOffer::$inactive ? 'selected' : '' }}>{{ trans('panel.inactive') }}</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class=" mt-4">
                                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

