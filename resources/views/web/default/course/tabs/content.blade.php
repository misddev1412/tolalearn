{{-- Sessions --}}
@if(!empty($course->sessions) and $course->sessions->count() > 0)
    <section class="mt-20">
        <h2 class="section-title after-line">{{ trans('public.sessions') }}</h2>

        <div class="mt-15">
            <div class="row">
                <div class="col-6 col-md-4 font-12 text-gray"><span class="pl-10">{{ trans('public.title') }}</span></div>
                <div class="col-3 font-12 text-gray text-center">{{ trans('public.start_date') }}</div>
                <div class="col-2 font-12 text-gray text-center d-none d-md-block">{{ trans('public.duration') }}</div>
                <div class="col-3"></div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="accordion-content-wrapper mt-15" id="sessionsAccordion" role="tablist" aria-multiselectable="true">
                        @foreach($course->sessions as $session)
                            <div class="accordion-row rounded-sm shadow-lg border mt-20 p-15">
                                <div class="row align-items-center" role="tab" id="session_{{ $session->id }}">
                                    <div class="col-6 col-md-4 d-flex align-items-center" href="#collapseSession{{ $session->id }}" aria-controls="collapseSession{{ $session->id }}" data-parent="#sessionsAccordion" role="button" data-toggle="collapse" aria-expanded="true">
                                        @if($session->date > time())
                                            <a href="{{ $session->addToCalendarLink() }}" target="_blank" class="mr-15 d-flex" data-toggle="tooltip" data-placement="top" title="{{ trans('public.add_to_calendar') }}">
                                                <i data-feather="bell" width="20" height="20" class="text-gray"></i>
                                            </a>
                                        @else
                                            <span class="mr-15 d-flex"><i data-feather="bell" width="20" height="20" class="text-gray"></i></span>
                                        @endif
                                        <span class="font-weight-bold text-secondary font-14">{{ $session->title }}</span>
                                    </div>
                                    <div class="col-3 text-gray text-center text-center font-14">{{ dateTimeFormat($session->date, 'j M Y | H:i') }}</div>
                                    <div class="col-2 text-gray text-center text-center font-14 d-none d-md-block">{{ convertMinutesToHourAndMinute($session->duration) }}</div>
                                    <div class="col-3 d-flex justify-content-end">
                                        @if($session->date < time())
                                            <button type="button" class="course-content-btns btn btn-sm btn-gray disabled flex-grow-1 disabled session-finished-toast">{{ trans('public.finished') }}</button>
                                        @elseif(empty($user))
                                            <button type="button" class="course-content-btns btn btn-sm btn-gray disabled flex-grow-1 disabled not-login-toast">{{ trans('public.go_to_class') }}</button>
                                        @elseif($hasBought)
                                            <a href="{{ $session->getJoinLink(true) }}" target="_blank" class="course-content-btns btn btn-sm btn-primary flex-grow-1">{{ trans('public.go_to_class') }}</a>
                                        @else
                                            <button type="button" class="course-content-btns btn btn-sm btn-gray flex-grow-1 disabled not-access-toast">{{ trans('public.go_to_class') }}</button>
                                        @endif
                                    </div>
                                </div>
                                <div id="collapseSession{{ $session->id }}" aria-labelledby="session_{{ $session->id }}" class=" collapse" role="tabpanel">
                                    <div class="panel-collapse">
                                        <div class="text-gray">
                                            {{ nl2br(clean($session->description)) }}
                                        </div>

                                        @if(!empty($user) and $hasBought)
                                            <div class="d-flex align-items-center mt-20">
                                                <label class="mb-0 mr-10 cursor-pointer font-weight-500" for="sessionReadToggle{{ $session->id }}">{{ trans('public.i_passed_this_lesson') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" @if($session->date < time()) disabled @endif id="sessionReadToggle{{ $session->id }}" data-session-id="{{ $session->id }}" value="{{ $course->id }}" class="js-text-session-toggle custom-control-input" @if(!empty($session->learningStatus)) checked @endif>
                                                    <label class="custom-control-label" for="sessionReadToggle{{ $session->id }}"></label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

{{-- Files --}}
@if(!empty($course->files) and $course->files->count() > 0)
    <section class="mt-40">
        <h2 class="section-title after-line">{{ trans('public.files') }}</h2>

        <div class="mt-15">
            <div class="row">
                <div class="col-9 col-md-6 font-12 text-gray"><span class="pl-10">{{ trans('public.title') }}</span></div>
                <div class="col-md-3 font-12 text-gray text-center d-none d-md-block">{{ trans('public.volume') }}</div>
                <div class="col-3"></div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="accordion-content-wrapper mt-15" id="filesAccordion" role="tablist" aria-multiselectable="true">
                        @foreach($course->files as $file)
                            <div class="accordion-row rounded-sm shadow-lg border mt-20 p-15">
                                <div class="row align-items-center" role="tab" id="files_{{ $file->id }}">
                                    <div class="col-9 col-md-6 d-flex align-items-center" href="#collapseFiles{{ $file->id }}" aria-controls="collapseFiles{{ $file->id }}" data-parent="#filesAccordion" role="button" data-toggle="collapse" aria-expanded="true">

                                        @if($file->accessibility == 'paid')
                                            @if(!empty($user) and $hasBought)
                                                @if($file->isVideo())
                                                    <button type="button" data-id="{{ $file->id }}" class="js-play-video btn-transparent mr-15" data-toggle="tooltip" data-placement="top" title="{{ trans('public.play_online') }}">
                                                        <i data-feather="play-circle" width="20" height="20" class="text-gray"></i>
                                                    </button>
                                                @else
                                                    <a href="{{ $course->getUrl() }}/file/{{ $file->id }}/download" class="mr-15">
                                                        <i data-feather="download-cloud" width="20" height="20" class="text-gray"></i>
                                                    </a>
                                                @endif
                                            @else
                                                <button class="mr-15 btn-transparent">
                                                    <i data-feather="lock" width="20" height="20" class="text-gray"></i>
                                                </button>
                                            @endif

                                        @else
                                            @if($file->isVideo())
                                                <button type="button" data-id="{{ $file->id }}" class="js-play-video btn-transparent mr-15" data-toggle="tooltip" data-placement="top" title="{{ trans('public.play_online') }}">
                                                    <i data-feather="play-circle" width="20" height="20" class="text-gray"></i>
                                                </button>
                                            @else
                                                <a href="{{ $course->getUrl() }}/file/{{ $file->id }}/download" class="mr-15" data-toggle="tooltip" data-placement="top" title="{{ trans('home.download') }}">
                                                    <i data-feather="download-cloud" width="20" height="20" class="text-gray"></i>
                                                </a>
                                            @endif
                                        @endif

                                        <span class="font-weight-bold text-secondary font-14 file-title">{{ $file->title }}</span>
                                    </div>

                                    <div class="col-md-3 text-gray font-14 text-center d-none d-md-block">{{ $file->volume }}</div>

                                    <div class="col-3 d-flex justify-content-end">
                                        @if($file->accessibility == 'paid')
                                            @if(!empty($user) and $hasBought)
                                                @if($file->downloadable)
                                                    <a href="{{ $course->getUrl() }}/file/{{ $file->id }}/download" class="course-content-btns btn btn-sm btn-primary flex-grow-1">
                                                        {{ trans('home.download') }}
                                                    </a>
                                                @else
                                                    <button type="button" data-id="{{ $file->id }}" class="js-play-video course-content-btns btn btn-sm btn-primary flex-grow-1">
                                                        {{ trans('public.play') }}
                                                    </button>
                                                @endif
                                            @else
                                                <button type="button" class="course-content-btns btn btn-sm btn-gray flex-grow-1 disabled {{ ((empty($user)) ? 'not-login-toast' : (!$hasBought ? 'not-access-toast' : '')) }}">
                                                    @if($file->downloadable)
                                                        {{ trans('home.download') }}
                                                    @else
                                                        {{ trans('public.play') }}
                                                    @endif
                                                </button>
                                            @endif

                                        @else
                                            @if($file->downloadable)
                                                <a href="{{ $course->getUrl() }}/file/{{ $file->id }}/download" class="course-content-btns btn btn-sm btn-primary flex-grow-1">
                                                    {{ trans('home.download') }}
                                                </a>
                                            @else
                                                <button type="button" data-id="{{ $file->id }}" class="js-play-video course-content-btns btn btn-sm btn-primary flex-grow-1">
                                                    {{ trans('public.play') }}
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <div id="collapseFiles{{ $file->id }}" aria-labelledby="files_{{ $file->id }}" class=" collapse" role="tabpanel">
                                    <div class="panel-collapse">
                                        <div class="text-gray text-14">
                                            {{ nl2br(clean($file->description)) }}
                                        </div>

                                        @if(!empty($user) and $hasBought)
                                            <div class="d-flex align-items-center mt-20">
                                                <label class="mb-0 mr-10 cursor-pointer font-weight-500" for="fileReadToggle{{ $file->id }}">{{ trans('public.i_passed_this_lesson') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" id="fileReadToggle{{ $file->id }}" data-file-id="{{ $file->id }}" value="{{ $course->id }}" class="js-file-learning-toggle custom-control-input" @if(!empty($file->learningStatus)) checked @endif>
                                                    <label class="custom-control-label" for="fileReadToggle{{ $file->id }}"></label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="playVideo" tabindex="-1" aria-labelledby="playVideoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-20">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="section-title after-line"></h3>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i data-feather="x" width="25" height="25"></i>
                    </button>
                </div>

                <div class="mt-25 position-relative">
                    <div class="loading-img py-50 text-center">
                        <img src="/assets/default/img/loading.gif" width="100" height="100">
                    </div>
                    <div class="js-modal-video-content d-none">

                    </div>
                </div>
            </div>
        </div>
    </div>

@endif

{{-- TextLessons --}}
@if(!empty($course->textLessons) and $course->textLessons->count() > 0)
    <section class="mt-40">
        <h2 class="section-title after-line">{{ trans('webinars.text_lessons') }}</h2>

        <div class="mt-15">
            <div class="row">
                <div class="col-7 col-md-5 font-12 text-gray"><span class="pl-10">{{ trans('public.title') }}</span></div>
                <div class="col-2 font-12 text-gray text-center">{{ trans('public.study_time') }}</div>
                <div class="col-2 font-12 text-gray text-center d-none d-md-block">{{ trans('public.attachments') }}</div>
                <div class="col-3"></div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="accordion-content-wrapper mt-15" id="textLessonsAccordion" role="tablist" aria-multiselectable="true">
                        @foreach($course->textLessons as $textLesson)
                            <div class="accordion-row rounded-sm shadow-lg border mt-20 p-15">
                                <div class="row align-items-center" role="tab" id="textLessons_{{ $textLesson->id }}">
                                    <div class="col-7 col-md-5 d-flex align-items-center" href="#collapseTextLessons{{ $textLesson->id }}" aria-controls="collapseTextLessons{{ $textLesson->id }}" data-parent="#textLessonsAccordion" role="button" data-toggle="collapse" aria-expanded="true">

                                        @if($textLesson->accessibility == 'paid')
                                            @if(!empty($user) and $hasBought)
                                                <a href="{{ $course->getUrl() }}/lessons/{{ $textLesson->id }}/read" target="_blank" class="mr-15" data-toggle="tooltip" data-placement="top" title="{{ trans('public.read') }}">
                                                    <i data-feather="file-text" width="20" height="20" class="text-gray"></i>
                                                </a>
                                            @else
                                                <button class="mr-15 btn-transparent">
                                                    <i data-feather="lock" width="20" height="20" class="text-gray"></i>
                                                </button>
                                            @endif
                                        @else
                                            <a href="{{ $course->getUrl() }}/lessons/{{ $textLesson->id }}/read" target="_blank" class="mr-15" data-toggle="tooltip" data-placement="top" title="{{ trans('public.read') }}">
                                                <i data-feather="file-text" width="20" height="20" class="text-gray"></i>
                                            </a>
                                        @endif

                                        <span class="font-weight-bold text-secondary font-14 file-title">{{ $textLesson->title }}</span>
                                    </div>

                                    <div class="col-2 text-gray text-center font-14">{{ $textLesson->study_time }} {{ trans('public.min') }}</div>

                                    <div class="col-2 text-gray text-center font-14 d-none d-md-block">{{ $textLesson->attachments_count }}</div>

                                    <div class="col-3 d-flex justify-content-end">
                                        @if($textLesson->accessibility == 'paid')
                                            @if(!empty($user) and $hasBought)
                                                <a href="{{ $course->getUrl() }}/lessons/{{ $textLesson->id }}/read" target="_blank" class="course-content-btns btn btn-sm btn-primary flex-grow-1">
                                                    {{ trans('public.read') }}
                                                </a>
                                            @else
                                                <button type="button" class="course-content-btns btn btn-sm btn-gray flex-grow-1 disabled {{ ((empty($user)) ? 'not-login-toast' : (!$hasBought ? 'not-access-toast' : '')) }}">
                                                    {{ trans('public.read') }}
                                                </button>
                                            @endif
                                        @else
                                            <a href="{{ $course->getUrl() }}/lessons/{{ $textLesson->id }}/read" target="_blank" class="course-content-btns btn btn-sm btn-primary flex-grow-1">
                                                {{ trans('public.read') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <div id="collapseTextLessons{{ $textLesson->id }}" aria-labelledby="textLessons_{{ $textLesson->id }}" class=" collapse" role="tabpanel">
                                    <div class="panel-collapse">
                                        <div class="text-gray">
                                            {{ nl2br(clean($textLesson->summary)) }}
                                        </div>

                                        @if(!empty($user) and $hasBought)
                                            <div class="d-flex align-items-center mt-20">
                                                <label class="mb-0 mr-10 cursor-pointer font-weight-500" for="textLessonReadToggle{{ $textLesson->id }}">{{ trans('public.i_passed_this_lesson') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" id="textLessonReadToggle{{ $textLesson->id }}" data-lesson-id="{{ $textLesson->id }}" value="{{ $course->id }}" class="js-text-lesson-learning-toggle custom-control-input" @if(!empty($textLesson->learningStatus)) checked @endif>
                                                    <label class="custom-control-label" for="textLessonReadToggle{{ $textLesson->id }}"></label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
{{-- Quizzes --}}
@if(!empty($course->quizzes) and $course->quizzes->count() > 0)
    <section class="mt-40">
        <h2 class="section-title after-line">{{ trans('quiz.quizzes') }}</h2>

        <div class="mt-15">
            <div class="row">
                <div class="col-7 col-md-3 font-12 text-gray"><span class="pl-10">{{ trans('public.title') }}</span></div>
                <div class="col-2 font-12 text-gray text-center">{{ trans('public.min') }} {{ trans('quiz.grade') }}</div>
                <div class="col-2 font-12 text-gray text-center d-none d-md-block">{{ trans('quiz.attempts') }}</div>
                <div class="col-2 font-12 text-gray text-center d-none d-md-block">{{ trans('public.status') }}</div>
                <div class="col-3"></div>
            </div>

            <div class="row">
                <div class="col-12">
                    @foreach($course->quizzes as $quiz)
                        <div class="rounded-sm shadow-lg border mt-20 p-15">
                            <div class="row align-items-center">
                                <div class="col-7 col-md-3 d-flex flex-column">
                                    <span class="font-weight-bold font-14 text-secondary">{{ $quiz->title }}</span>
                                    <span class="font-12 text-gray">{{ $quiz->quizQuestions->count() }} {{ trans('public.questions') }}, {{ $quiz->time }} {{ trans('public.min') }}</span>
                                </div>

                                <div class="col-2 text-gray font-14 text-center">{{ $quiz->pass_mark }}/{{ $quiz->quizQuestions->sum('grade') }}</div>

                                <div class="col-2 text-gray font-14 text-center d-none d-md-block">{{ (!empty($user) and !empty($quiz->result_count)) ? $quiz->result_count : '0' }}/{{ $quiz->attempt }}</div>

                                @if(empty($user) or empty($quiz->result_status))
                                    <div class="col-2 text-gray font-14 text-center d-none d-md-block">-</div>
                                @else
                                    <div class="col-2 text-gray text-center d-none d-md-block">
                                        <div class="d-flex flex-column @if($quiz->result_status == 'passed') text-primary @elseif($quiz->result_status == 'failed') text-danger @else text-warning @endif">
                                            @if($quiz->result_status == 'passed')
                                                <span class="font-14">{{ trans('quiz.passed') }}</span>
                                            @elseif($quiz->result_status == 'failed')
                                                <span class="font-14">{{ trans('quiz.failed') }}</span>
                                            @elseif($quiz->result_status == 'waiting')
                                                <span class="font-14">{{ trans('quiz.waiting') }}</span>
                                            @endif

                                            <span class="font-14">({{ $quiz->user_grade }}/{{ $quiz->quizQuestions->sum('grade') }})</span>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-3 d-flex justify-content-end">
                                    @if(!empty($user) and $quiz->can_try and $hasBought)
                                        <a href="/panel/quizzes/{{ $quiz->id }}/start" class="course-content-btns btn btn-sm btn-primary flex-grow-1">{{ trans('quiz.quiz_start') }}</a>
                                    @else
                                        <button type="button" class="course-content-btns btn btn-sm btn-gray flex-grow-1 disabled {{ ((empty($user)) ? 'not-login-toast' : (!$hasBought ? 'not-access-toast' : (!$quiz->can_try ? 'can-not-try-again-quiz-toast' : ''))) }}">
                                            {{ trans('quiz.quiz_start') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
{{-- Certificates --}}
@if(!empty($course->quizzes) and $course->quizzes->count() > 0 and ($quiz->certificate) )
    <section class="mt-40">
        <h2 class="section-title after-line">{{ trans('panel.certificates') }}</h2>

        <div class="mt-15">
            <div class="row">
                <div class="col-6 font-12 text-gray"><span class="pl-10">{{ trans('public.title') }}</span></div>
                <div class="col-3 text-center font-12 text-gray">{{ trans('public.min') }} {{ trans('quiz.grade') }}</div>
                <div class="col-3"></div>
            </div>

            <div class="row">
                <div class="col-12">
                    @foreach($course->quizzes as $quiz)
                        @if($quiz->certificate)
                            <div class="rounded-sm shadow-lg border mt-20 p-15">
                                <div class="row align-items-center">
                                    <div class="col-6 d-flex flex-column">
                                        <span class="font-weight-bold font-14 text-secondary">{{ $quiz->title }}</span>
                                    </div>

                                    <div class="col-3 text-gray font-14 text-center">{{ $quiz->pass_mark }}/{{ $quiz->quizQuestions->sum('grade') }}</div>

                                    <div class="col-3 d-flex justify-content-end">
                                        @if(!empty($user) and $quiz->can_download_certificate and $hasBought)
                                            <a href="/panel/quizzes/results/{{ $quiz->result->id }}/downloadCertificate" class="course-content-btns btn btn-sm btn-primary flex-grow-1">{{ trans('home.download') }}</a>
                                        @else
                                            <button type="button" class="course-content-btns btn btn-sm btn-gray flex-grow-1 disabled {{ ((empty($user)) ? 'not-login-toast' : (!$hasBought ? 'not-access-toast' : (!$quiz->can_download_certificate ? 'can-not-download-certificate-toast' : ''))) }}">
                                                {{ trans('home.download') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
