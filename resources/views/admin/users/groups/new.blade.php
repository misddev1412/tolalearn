@extends('admin.layouts.app')

@push('styles_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.new_user_group') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/admin/">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.new_user_group') }}</div>
            </div>
        </div>


        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-8 col-lg-6">
                                    <form action="/admin/users/groups/{{ !empty($group) ? $group->id.'/update' : 'store' }}" method="Post">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <label>{{ trans('admin/main.name') }}</label>
                                            <input type="text" name="name"
                                                   class="form-control  @error('name') is-invalid @enderror"
                                                   value="{{ !empty($group) ? $group->name : old('name') }}"/>
                                            @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group ">
                                            <label>{{ trans('admin/main.user_group_commission_rate') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-percentage"></i>
                                                    </div>
                                                </div>

                                                <input type="number"
                                                       name="commission"
                                                       class="spinner-input form-control text-center @error('commission') is-invalid @enderror"
                                                       value="{{ !empty($group) ? $group->commission : old('commission') }}"
                                                       placeholder="{{ trans('admin/main.user_group_commission_rate_placeholder') }}" maxlength="3" min="0" max="100">

                                                @error('commission')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                              <div class="text-muted text-small mt-1">{{ trans('admin/main.user_group_commission_rate_hint') }}</div>
                                        </div>

                                        <div class="form-group ">
                                            <label>{{ trans('admin/main.user_group_discount_rate') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-percentage"></i>
                                                    </div>
                                                </div>
                                                <input type="number"
                                                       name="discount"
                                                       class="form-control spinner-input text-center @error('discount') is-invalid @enderror"
                                                       value="{{ !empty($group) ? $group->discount : old('discount') }}"
                                                       placeholder="{{ trans('admin/main.user_group_discount_rate_placeholder') }}" maxlength="3" min="0" max="100">
                                                @error('discount')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                             <div class="text-muted text-small mt-1">{{ trans('admin/main.user_group_discount_rate_hint') }}</div>
                                        </div>


                                        <div class="form-group">
                                            <label class="input-label d-block">{{ trans('admin/main.users') }}</label>
                                            <select name="users[]" multiple="multiple" class="form-control search-user-select2"
                                                    data-search-option="for_user_group"
                                                    data-placeholder="{{ trans('public.search_user') }}">

                                                @if(!empty($userGroups) and $userGroups->count() > 0)
                                                    @foreach($userGroups as $userGroup)
                                                        <option value="{{ $userGroup->user_id }}" selected>{{ $userGroup->user->full_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group custom-switches-stacked">
                                            <label class="custom-switch pl-0">
                                                <input type="hidden" name="status" value="inactive">
                                                <input type="checkbox" name="status" id="preloadingSwitch" value="active" {{ (!empty($group) and $group->status == 'active') ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                                                <span class="custom-switch-indicator"></span>
                                                <label class="custom-switch-description mb-0 cursor-pointer" for="preloadingSwitch">{{ trans('admin/main.active') }}</label>
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
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')

@endpush
