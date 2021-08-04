<div id="multipleQuestionModal" class="{{ empty($question_edit) ? 'd-none' : ''}}">
    <div class="custom-modal-body">
        <h2 class="section-title after-line">{{ trans('quiz.multiple_choice_question') }}</h2>

        <form action="/admin/quizzes-questions/{{ empty($question_edit) ? 'store' : $question_edit->id.'/update' }}" method="post">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="quiz_id" value="{{ !empty($quiz) ? $quiz->id :'' }}">
            <input type="hidden" name="type" value="{{ \App\Models\QuizzesQuestion::$multiple }}">

            <div class="row mt-3">
                <div class="col-12 col-md-8">
                    <div class="form-group">
                        <label class="input-label">{{ trans('quiz.question_title') }}</label>
                        <input type="text" name="title" class="form-control" value="{{ !empty($question_edit) ? $question_edit->title : '' }}"/>
                        <span class="invalid-feedback"></span>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="input-label">{{ trans('quiz.grade') }}</label>
                        <input type="text" name="grade" class="form-control" value="{{ !empty($question_edit) ? $question_edit->grade : '' }}"/>
                        <span class="invalid-feedback"></span>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <h2 class="section-title after-line">{{ trans('public.answers') }}</h2>

                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-sm btn-primary mt-2 add-answer-btn">{{ trans('quiz.add_an_answer') }}</button>

                    <div class="form-group">
                        <input type="hidden" name="current_answer" class="form-control"/>
                        <span class="invalid-feedback"></span>
                    </div>
                </div>
            </div>

            <div class="add-answer-container">

                @if (!empty($question_edit->quizzesQuestionsAnswers) and !$question_edit->quizzesQuestionsAnswers->isEmpty())
                    @foreach ($question_edit->quizzesQuestionsAnswers as $answer)
                        @include('admin.quizzes.modals.multiple_answer_form',['answer' => $answer])
                    @endforeach
                @else
                    @include('admin.quizzes.modals.multiple_answer_form')
                @endif
            </div>

            <div class="d-flex align-items-center justify-content-end mt-3">
                <button type="button" class="save-question btn btn-sm btn-primary">{{ trans('public.save') }}</button>
                <button type="button" class="close-swl btn btn-sm btn-danger ml-2">{{ trans('public.close') }}</button>
            </div>

        </form>
    </div>
</div>
