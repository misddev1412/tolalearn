<div class="">
    <div data-action="{{ !empty($quiz) ? '/panel/quizzes/'. $quiz->id .'/update' : '/panel/quizzes/store' }}" class="quiz-form webinar-form">

        <section>
            <h2 class="section-title after-line">{{ trans('quiz.new_quiz') }}</h2>

            <div class="row">
                <div class="col-12 col-md-4">

                    @if(empty($selectedWebinar))
                        <div class="form-group mt-25">
                            <label class="input-label">{{ trans('panel.webinar') }}</label>
                            <select name="ajax[webinar_id]" class="js-ajax-webinar_id custom-select">
                                <option {{ !empty($quiz) ? 'disabled' : 'selected disabled' }} value="">{{ trans('panel.choose_webinar') }}</option>
                                @foreach($webinars as $webinar)
                                    <option value="{{ $webinar->id }}" {{  (!empty($quiz) and $quiz->webinar_id == $webinar->id) ? 'selected' : '' }}>{{ $webinar->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="ajax[webinar_id]" value="{{ $selectedWebinar->id }}">
                    @endif

                    <div class="form-group @if(!empty($selectedWebinar)) mt-25 @endif">
                        <label class="input-label">{{ trans('quiz.quiz_title') }}</label>
                        <input type="text" value="{{ !empty($quiz) ? $quiz->title : old('title') }}" name="ajax[title]" class="js-ajax-title form-control @error('title')  is-invalid @enderror" placeholder=""/>
                        <div class="invalid-feedback">
                            @error('title')
                            {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label">{{ trans('public.time') }} <span class="braces">({{ trans('public.minutes') }})</span></label>
                        <input type="text" value="{{ !empty($quiz) ? $quiz->time : old('time') }}" name="ajax[time]" class="js-ajax-time form-control @error('time')  is-invalid @enderror" placeholder="{{ trans('forms.empty_means_unlimited') }}"/>
                        <div class="invalid-feedback">
                            @error('time')
                            {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label">{{ trans('quiz.number_of_attemps') }}</label>
                        <input type="text" name="ajax[attempt]" value="{{ !empty($quiz) ? $quiz->attempt : old('attempt') }}" class="js-ajax-attempt form-control @error('attempt')  is-invalid @enderror" placeholder="{{ trans('forms.empty_means_unlimited') }}"/>
                        <div class="invalid-feedback">
                            @error('attempt')
                            {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label">{{ trans('quiz.pass_mark') }}</label>
                        <input type="text" name="ajax[pass_mark]" value="{{ !empty($quiz) ? $quiz->pass_mark : old('pass_mark') }}" class="js-ajax-pass_mark form-control @error('pass_mark')  is-invalid @enderror" placeholder=""/>
                        <div class="invalid-feedback">
                            @error('pass_mark')
                            {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mt-20 d-flex align-items-center justify-content-between">
                        <label class="cursor-pointer input-label" for="certificateSwitch{{ $quiz ?? '' }}">{{ trans('quiz.certificate_included') }}</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="ajax[certificate]" class="js-ajax-certificate custom-control-input" id="certificateSwitch{{ $quiz ?? '' }}" {{ !empty($quiz) && $quiz->certificate ? 'checked' : ''}}>
                            <label class="custom-control-label" for="certificateSwitch{{ $quiz ?? '' }}"></label>
                        </div>
                    </div>

                    <div class="form-group mt-20 d-flex align-items-center justify-content-between">
                        <label class="cursor-pointer input-label" for="statusSwitch{{ $quiz ?? '' }}">{{ trans('quiz.active_quiz') }}</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="ajax[status]" class="js-ajax-status custom-control-input" id="statusSwitch{{ $quiz ?? '' }}" {{ !empty($quiz) && $quiz->status ? 'checked' : ''}}>
                            <label class="custom-control-label" for="statusSwitch{{ $quiz ?? '' }}"></label>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        @if(!empty($quiz))
            <section class="mt-30">
                <div class="d-block d-md-flex justify-content-between align-items-center pb-20">
                    <h2 class="section-title after-line">{{ trans('public.questions') }}</h2>

                    <div class="d-flex align-items-center mt-20 mt-md-0">
                        <button id="add_multiple_question" data-quiz-id="{{ $quiz->id }}" type="button" class="btn btn-primary btn-sm ml-10">{{ trans('quiz.add_multiple_choice') }}</button>
                        <button id="add_descriptive_question" data-quiz-id="{{ $quiz->id }}" type="button" class="btn btn-primary btn-sm ml-10">{{ trans('quiz.add_descriptive') }}</button>
                    </div>
                </div>

                @if($quizQuestions)
                    @foreach($quizQuestions as $question)
                        <div class="quiz-question-card d-flex align-items-center mt-20">
                            <div class="flex-grow-1">
                                <h4 class="question-title">{{ $question->title }}</h4>
                                <div class="font-12 mt-5 question-infos">
                                    <span>{{ $question->type === App\Models\QuizzesQuestion::$multiple ? trans('quiz.multiple_choice') : trans('quiz.descriptive') }} | {{ trans('quiz.grade') }}: {{ $question->grade }}</span>
                                </div>
                            </div>

                            <div class="btn-group dropdown table-actions">
                                <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i data-feather="more-vertical" height="20"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <button type="button" data-question-id="{{ $question->id }}" class="edit_question btn btn-sm btn-transparent d-block">{{ trans('public.edit') }}</button>
                                    <a href="/panel/quizzes-questions/{{ $question->id }}/delete" class="delete-action btn btn-sm btn-transparent d-block">{{ trans('public.delete') }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </section>
        @endif

        <input type="hidden" name="ajax[is_webinar_page]" value="@if(!empty($inWebinarPage) and $inWebinarPage) 1 @else 0 @endif">

        <div class="mt-20 mb-20">
            @if(!empty($inWebinarPage) and $inWebinarPage)
                <button type="button" class="js-submit-quiz-form btn btn-sm btn-primary">{{ !empty($quiz) ? trans('public.save_change') : trans('public.create') }}</button>
            @else
                <button type="button" class="js-submit-quiz-form btn btn-sm btn-primary">{{ !empty($quiz) ? trans('public.save_change') : trans('public.create') }}</button>
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
@if(!empty($quiz))
    @include(getTemplate() .'.panel.quizzes.modals.multiple_question',['quiz' => $quiz])
    @include(getTemplate() .'.panel.quizzes.modals.descriptive_question',['quiz' => $quiz])
@endif
