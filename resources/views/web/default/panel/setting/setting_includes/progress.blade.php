@php
    $progressSteps = [
        1 => [
            'name' => 'basic_information',
            'icon' => 'basic-info'
        ],

        2 => [
            'name' => 'images',
            'icon' => 'images'
        ],

        3 => [
            'name' => 'about',
            'icon' => 'about'
        ],

        4 => [
            'name' => 'educations',
            'icon' => 'graduate'
        ],

        5 => [
            'name' => 'experiences',
            'icon' => 'experiences'
        ],

        6 => [
            'name' => 'occupations',
            'icon' => 'skills'
        ],
    ];

    if(!$user->isUser()) {
        $progressSteps[7] =[
            'name' => 'identity_and_financial',
            'icon' => 'financial'
        ];

        $progressSteps[8] =[
            'name' => 'zoom_api',
            'icon' => 'zoom'
        ];
    }

    $currentStep = empty($currentStep) ? 1 : $currentStep;
@endphp


<div class="webinar-progress d-block d-lg-flex align-items-center p-15 panel-shadow bg-white rounded-sm">

    @foreach($progressSteps as $key => $step)
        <div class="progress-item d-flex align-items-center">
            <a href="@if(!empty($organization_id)) /panel/manage/{{ $user_type ?? 'instructors' }}/{{ $user->id }}/edit/step/{{ $key }} @else /panel/setting/step/{{ $key }} @endif" class="progress-icon p-10 d-flex align-items-center justify-content-center rounded-circle {{ $key == $currentStep ? 'active' : '' }}" data-toggle="tooltip" data-placement="top" title="{{ trans('public.' . $step['name']) }}">
                <img src="/assets/default/img/icons/{{ $step['icon'] }}.svg" class="img-cover" alt="">
            </a>

            <div class="ml-10 {{ $key == $currentStep ? '' : 'd-lg-none' }}">
                <h4 class="font-16 text-secondary font-weight-bold">{{ trans('public.' . $step['name']) }}</h4>
            </div>
        </div>
    @endforeach
</div>
