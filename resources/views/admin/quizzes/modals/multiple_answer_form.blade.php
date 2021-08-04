<div class="add-answer-card mt-4 {{ (empty($answer) or (!empty($loop) and $loop->iteration == 1)) ? 'main-answer-row' : '' }}">
    <button type="button" class="btn btn-sm btn-danger rounded-circle answer-remove {{ (!empty($answer) and !empty($loop) and $loop->iteration > 1) ? '' : 'd-none' }}">
        <i class="fa fa-times"></i>
    </button>

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label class="input-label">{{ trans('quiz.answer_title') }}</label>
                <input type="text" name="answers[{{ !empty($answer) ? $answer->id : 'record' }}][title]" class="form-control" value="{{ !empty($answer) ? $answer->title : '' }}"/>
            </div>
        </div>
    </div>

    <div class="row mt-2 align-items-end">
        <div class="col-12 col-md-8">
            <div class="form-group">
                <label class="input-label">{{ trans('quiz.answer_image') }} <span class="braces">({{ trans('public.optional') }})</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="button" class="input-group-text panel-file-manager" data-input="file{{ !empty($answer) ? $answer->id : '' }}" data-preview="holder">
                            <i class="fa fa-arrow-up" class="text-white"></i>
                        </button>
                    </div>
                    <input id="file{{ !empty($answer) ? $answer->id : '' }}" type="text" name="answers[{{ !empty($answer) ? $answer->id : 'record' }}][file]" value="{{ !empty($answer) ? $answer->image : '' }}" class="form-control lfm-input"/>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group mt-2 d-flex align-items-center justify-content-between js-switch-parent">
                <label class="js-switch" for="correctAnswerSwitch{{ !empty($answer) ? $answer->id : '' }}">{{ trans('quiz.correct_answer') }}</label>
                <div class="custom-control custom-switch">
                    <input id="correctAnswerSwitch{{ !empty($answer) ? $answer->id : '' }}" type="checkbox" name="answers[{{ !empty($answer) ? $answer->id : 'record' }}][correct]" @if(!empty($answer) and $answer->correct) checked @endif class="custom-control-input js-switch">
                    <label class="custom-control-label js-switch" for="correctAnswerSwitch{{ !empty($answer) ? $answer->id : '' }}"></label>
                </div>
            </div>
        </div>
    </div>
</div>
