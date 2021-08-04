@extends(getTemplate() .'.panel.layouts.panel_layout')

@section('content')
    @if(empty($new_user))
        @include('web.default.panel.setting.setting_includes.progress')
    @endif

    <form method="post" id="userSettingForm" class="mt-30" action="{{ (!empty($new_user)) ? '/panel/manage/'. $user_type .'/new' : '/panel/setting' }}">
        {{ csrf_field() }}
        <input type="hidden" name="step" value="{{ !empty($currentStep) ? $currentStep : 1 }}">
        <input type="hidden" name="next_step" value="0">

        @if(!empty($organization_id))
            <input type="hidden" name="organization_id" value="{{ $organization_id }}">
            <input type="hidden" id="userId" name="user_id" value="{{ $user->id }}">
        @endif

        @if(!empty($new_user) or (!empty($currentStep) and $currentStep == 1))
            @include('web.default.panel.setting.setting_includes.basic_information')
        @endif

        @if(empty($new_user) and !empty($currentStep))
            @switch($currentStep)
                @case(2)
                @include('web.default.panel.setting.setting_includes.image')
                @break

                @case(3)
                @include('web.default.panel.setting.setting_includes.about')
                @break

                @case(4)
                @include('web.default.panel.setting.setting_includes.education')
                @break

                @case(5)
                @include('web.default.panel.setting.setting_includes.experiences')
                @break

                @case(6)
                @include('web.default.panel.setting.setting_includes.occupations')
                @break

                @case(7)
                @if(!$user->isUser())
                    @include('web.default.panel.setting.setting_includes.identity_and_financial')
                @endif
                @break

                @case(8)
                @if(!$user->isUser())
                    @include('web.default.panel.setting.setting_includes.zoom_api')
                @endif
                @break
            @endswitch
        @endif
    </form>

    <div class="create-webinar-footer d-flex align-items-center justify-content-between mt-20 pt-15 border-top">
        <div class="">
            @if(!empty($user))
                @if(!empty($currentStep) and $currentStep > 1)
                    <a href="/panel/setting/step/{{ ($currentStep - 1) }}" class="btn btn-sm btn-primary">{{ trans('webinars.previous') }}</a>
                @else
                    <a href="" class="btn btn-sm btn-primary disabled">{{ trans('webinars.previous') }}</a>
                @endif

                <button type="button" id="getNextStep" class="btn btn-sm btn-primary ml-15" @if(!empty($currentStep) and $currentStep == 8) disabled @endif>{{ trans('webinars.next') }}</button>
            @endif
        </div>

        <button type="button" id="saveData" class="btn btn-sm btn-primary ml-15">{{ trans('public.save') }}</button>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/vendors/cropit/jquery.cropit.js"></script>
    <script src="/assets/default/js/parts/img_cropit.min.js"></script>

    <script>
        ;(function (){ 
        'use strict'
        var editEducationLang = '{{ trans('site.edit_education') }}';
        var editExperienceLang = '{{ trans('site.edit_experience') }}';
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        var saveErrorLang = '{{ trans('site.store_error_try_again') }}';
        var notAccessToLang = '{{ trans('public.not_access_to_this_content') }}';
        }())
    </script>

    <script src="/assets/default/js/panel/user_setting.min.js"></script>
@endpush
