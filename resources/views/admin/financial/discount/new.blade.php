@extends('admin.layouts.app')

@push('styles_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/admin/">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.discounts') }}</div>
            </div>
        </div>


        <div class="section-body">

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-8 col-lg-6">
                            <form action="/admin/financial/discounts/{{ !empty($discount) ? $discount->id.'/update' : 'store' }}" method="Post">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label>{{ trans('admin/main.title') }}</label>
                                    <input type="text" name="title"
                                           class="form-control  @error('title') is-invalid @enderror"
                                           value="{{ !empty($discount) ? $discount->title : old('title') }}"/>
                                    @error('title')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="input-label d-block">{{ trans('admin/main.users') }}</label>
                                    <select name="user_id" class="form-control search-user-select2"
                                            data-placeholder="Search and Select User  (for all users take it empty)">

                                        @if(!empty($userDiscounts) && $userDiscounts->count() > 0)
                                            @foreach($userDiscounts as $userDiscount)
                                                <option value="{{ $userDiscount->user_id }}" selected>{{ $userDiscount->user->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label>{{ trans('admin/main.discount_percentage') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-percentage" href=""></i>
                                            </div>
                                        </div>

                                        <input type="number" name="percent"
                                               class="form-control text-center  @error('percent') is-invalid @enderror"
                                               value="{{ !empty($discount) ? $discount->percent : old('percent') }}"
                                               placeholder="0"/>
                                        @error('percent')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ trans('admin/main.max_amount') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-dollar-sign" href=""></i>
                                            </div>
                                        </div>

                                        <input type="number" name="amount"
                                               class="form-control text-center @error('amount') is-invalid @enderror"
                                               value="{{ !empty($discount) ? $discount->amount : old('amount') }}"
                                               placeholder="{{ trans('admin/main.amount_placeholder') }}"/>
                                        @error('amount')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ trans('admin/main.usable_times') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-users" href=""></i>
                                            </div>
                                        </div>

                                        <input type="number" name="count"
                                               class="form-control text-center @error('count') is-invalid @enderror"
                                               value="{{ !empty($discount) ? $discount->count : old('count') }}"
                                               placeholder="{{ trans('admin/main.count_placeholder') }}"/>
                                        @error('count')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="inputDefault">{{ trans('admin/main.discount_code') }}</label>
                                    <input type="text" name="code"
                                           value="{{ !empty($discount) ? $discount->code : old('code') }}"
                                           class="form-control text-center @error('code') is-invalid @enderror">
                                    @error('code')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                    <div class="text-muted text-small mt-1">{{ trans('admin/main.discount_code_hint') }}</div>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.expiration') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="dateRangeLabel">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="expired_at" class="form-control datetimepicker"
                                               aria-describedby="dateRangeLabel"
                                               value="{{ !empty($discount) ? dateTimeFormat($discount->expired_at,'Y-m-d H:i') : '' }}"/>
                                        <div class="invalid-feedback"></div>
                                    </div>
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

