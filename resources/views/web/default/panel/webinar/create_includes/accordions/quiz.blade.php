<div class="accordion-row bg-white rounded-sm panel-shadow mt-20 py-15 py-lg-30 px-10 px-lg-20">
    <div class="d-flex align-items-center justify-content-between " role="tab" id="quiz_{{ !empty($quizInfo) ? $quizInfo->id :'record' }}">
        <div class="font-weight-bold text-dark-blue" href="#collapseQuiz{{ !empty($quizInfo) ? $quizInfo->id :'record' }}" aria-controls="collapseQuiz{{ !empty($quizInfo) ? $quizInfo->id :'record' }}" data-parent="#quizzesAccordion" role="button" data-toggle="collapse" aria-expanded="true">
            <span>{{ !empty($quizInfo) ? $quizInfo->title : trans('public.add_new_quizzes') }}</span>
        </div>

        <div class="d-flex align-items-center">
            <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseQuiz{{ !empty($quizInfo) ? $quizInfo->id :'record' }}" aria-controls="collapseQuiz{{ !empty($quizInfo) ? $quizInfo->id :'record' }}" data-parent="#quizzesAccordion" role="button" data-toggle="collapse" aria-expanded="true"></i>
        </div>
    </div>

    <div id="collapseQuiz{{ !empty($quizInfo) ? $quizInfo->id :'record' }}" aria-labelledby="quiz_{{ !empty($quizInfo) ? $quizInfo->id :'record' }}" class=" collapse @if(empty($quizInfo)) show @endif" role="tabpanel">
        <div class="panel-collapse text-gray">
            @include('web.default.panel.quizzes.create_quiz_form',
                    [
                        'inWebinarPage' => true,
                        'selectedWebinar' => $webinar,
                        'quiz' => $quizInfo ?? null,
                        'quizQuestions' => !empty($quizInfo) ? $quizInfo->quizQuestions : [],
                    ]
                )
        </div>
    </div>
</div>
