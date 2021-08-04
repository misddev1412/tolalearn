@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
@endpush

@section('content')

    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.quizzes') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/admin/">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.quizzes') }}</div>
            </div>
        </div>

        <div class="section-body">


            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="{{ !empty($quiz) ? '/admin/quizzes/'. $quiz->id .'/update' : '/admin/quizzes/store' }}" id="webinarForm" class="webinar-form">
                                {{ csrf_field() }}
                                <section>

                                    <div class="row">
                                        <div class="col-12 col-md-4">


                                        <div class="d-flex align-items-center justify-content-between">
                <div class="">
                    <h2 class="section-title">{{ trans('quiz.edit_quiz') }} - {{ $quiz->title }}</h2>
                    <p>{{ trans('admin/main.instructor') }}: {{ $creator->full_name }}</p>
                </div>
            </div>

                                            <div class="form-group mt-3">
                                                <label class="input-label">{{ trans('panel.webinar') }}</label>
                                                <select name="webinar_id" class="custom-select">
                                                    <option {{ !empty($quiz) ? 'disabled' : 'selected disabled' }} value="">{{ trans('panel.choose_webinar') }}</option>
                                                    @foreach($webinars as $webinar)
                                                        <option value="{{ $webinar->id }}" {{  (!empty($quiz) and $quiz->webinar_id == $webinar->id) ? 'selected' : '' }}>{{ $webinar->title }}</option>
                                                    @endforeach
                                                </select>

                                            </div>

                                            <div class="form-group">
                                                <label class="input-label">{{ trans('quiz.quiz_title') }}</label>
                                                <input type="text" value="{{ !empty($quiz) ? $quiz->title : old('title') }}" name="title" class="form-control @error('title')  is-invalid @enderror" placeholder=""/>
                                                @error('title')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label class="input-label">{{ trans('public.time') }} <span class="braces">({{ trans('public.minutes') }})</span></label>
                                                <input type="text" value="{{ !empty($quiz) ? $quiz->time : old('time') }}" name="time" class="form-control @error('time')  is-invalid @enderror" placeholder="{{ trans('forms.empty_means_unlimited') }}"/>
                                                @error('time')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label class="input-label">{{ trans('quiz.number_of_attemps') }}</label>
                                                <input type="text" name="attempt" value="{{ !empty($quiz) ? $quiz->attempt : old('attempt') }}" class="form-control @error('attempt')  is-invalid @enderror" placeholder="{{ trans('forms.empty_means_unlimited') }}"/>
                                                @error('attempt')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label class="input-label">{{ trans('quiz.pass_mark') }}</label>
                                                <input type="text" name="pass_mark" value="{{ !empty($quiz) ? $quiz->pass_mark : old('pass_mark') }}" class="form-control @error('pass_mark')  is-invalid @enderror" placeholder=""/>
                                                @error('pass_mark')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="form-group mt-4 d-flex align-items-center justify-content-between">
                                                <label class="cursor-pointer" for="certificateSwitch">{{ trans('quiz.certificate_included') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="certificate" class="custom-control-input" id="certificateSwitch" {{ !empty($quiz) && $quiz->certificate ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="certificateSwitch"></label>
                                                </div>
                                            </div>

                                            <div class="form-group mt-4 d-flex align-items-center justify-content-between">
                                                <label class="cursor-pointer" for="statusSwitch">{{ trans('quiz.active_quiz') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="status" class="custom-control-input" id="statusSwitch" {{ !empty($quiz) && $quiz->status ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="statusSwitch"></label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </section>
                                @if(!empty($quiz))
                                    <section class="mt-5">
                                        <div class="d-flex justify-content-between align-items-center pb-20">
                                            <h2 class="section-title after-line">{{ trans('public.questions') }}</h2>
                                            <button id="add_multiple_question" type="button" class="btn btn-primary btn-sm ml-2 mt-3">{{ trans('quiz.add_multiple_choice') }}</button>
                                            <button id="add_descriptive_question" type="button" class="btn btn-primary btn-sm ml-2 mt-3">{{ trans('quiz.add_descriptive') }}</button>
                                        </div>
                                        @if($quizQuestions)
                                            @foreach($quizQuestions as $question)
                                                <div class="quiz-question-card d-flex align-items-center mt-4">
                                                    <div class="flex-grow-1">
                                                        <h4 class="question-title">{{ $question->title }}</h4>
                                                        <div class="font-12 mt-3 question-infos">
                                                            <span>{{ $question->type === App\Models\QuizzesQuestion::$multiple ? trans('quiz.multiple_choice') : trans('quiz.descriptive') }} | {{ trans('quiz.grade') }}: {{ $question->grade }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="btn-group dropdown table-actions">
                                                        <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fa fa-ellipsis-v"></i>
                                                        </button>
                                                        <div class="dropdown-menu text-left">
                                                            <button type="button" data-question-id="{{ $question->id }}" class="edit_question btn btn-sm btn-transparent">{{ trans('public.edit') }}</button>
                                                            @include('admin.includes.delete_button',['url' => '/admin/quizzes-questions/'. $question->id .'/delete', 'btnClass' => 'btn-sm btn-transparent' , 'btnText' => trans('public.delete')])
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </section>
                                @endif
                                <div class="mt-5 mb-5">
                                    <button type="submit" class="btn btn-primary">{{ !empty($quiz) ? trans('admin/main.save_change') : trans('admin/main.create') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    @include('admin.quizzes.modals.multiple_question')
    @include('admin.quizzes.modals.descriptive_question')
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>

    <script>
        ;(function (){ 
        'use strict'
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        }())
    </script>

    <script src="/assets/default/js/admin/quiz.min.js"></script>
@endpush
