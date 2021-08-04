<li data-id="{{ !empty($textLesson) ? $textLesson->id :'' }}" class="accordion-row bg-white rounded-sm panel-shadow mt-20 py-15 py-lg-30 px-10 px-lg-20">
    <div class="d-flex align-items-center justify-content-between " role="tab" id="text_lesson_{{ !empty($textLesson) ? $textLesson->id :'record' }}">
        <div class="font-weight-bold text-dark-blue" href="#collapseTextLesson{{ !empty($textLesson) ? $textLesson->id :'record' }}" aria-controls="collapseTextLesson{{ !empty($textLesson) ? $textLesson->id :'record' }}" data-parent="#text_lessonsAccordion" role="button" data-toggle="collapse" aria-expanded="true">
            <span>{{ !empty($textLesson) ? $textLesson->title : trans('public.add_new_test_lesson') }}</span>
        </div>

        <div class="d-flex align-items-center">
            <i data-feather="move" class="move-icon mr-10 cursor-pointer" height="20"></i>

            @if(!empty($textLesson))
                <div class="btn-group dropdown table-actions mr-15">
                    <button type="button" class="btn-transparent dropdown-toggle d-flex align-items-center justify-content-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i data-feather="more-vertical" height="20"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="/panel/text-lesson/{{ $textLesson->id }}/delete" class="delete-action btn btn-sm btn-transparent">{{ trans('public.delete') }}</a>
                    </div>
                </div>
            @endif

            <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseTextLesson{{ !empty($textLesson) ? $textLesson->id :'record' }}" aria-controls="collapseTextLesson{{ !empty($textLesson) ? $textLesson->id :'record' }}" data-parent="#text_lessonsAccordion" role="button" data-toggle="collapse" aria-expanded="true"></i>
        </div>
    </div>

    <div id="collapseTextLesson{{ !empty($textLesson) ? $textLesson->id :'record' }}" aria-labelledby="text_lesson_{{ !empty($textLesson) ? $textLesson->id :'record' }}" class=" collapse @if(empty($textLesson)) show @endif" role="tabpanel">
        <div class="panel-collapse text-gray">
            <div class="text_lesson-form" data-action="/panel/text-lesson/{{ !empty($textLesson) ? $textLesson->id . '/update' : 'store' }}">
                <input type="hidden" name="ajax[{{ !empty($textLesson) ? $textLesson->id : 'new' }}][webinar_id]" value="{{ !empty($webinar) ? $webinar->id :'' }}">

                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="input-label">{{ trans('public.title') }}</label>
                            <input type="text" name="ajax[{{ !empty($textLesson) ? $textLesson->id : 'new' }}][title]" class="js-ajax-title form-control" value="{{ !empty($textLesson) ? $textLesson->title : '' }}" placeholder=""/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.study_time') }} (Min)</label>
                            <input type="text" name="ajax[{{ !empty($textLesson) ? $textLesson->id : 'new' }}][study_time]" class="js-ajax-study_time form-control" value="{{ !empty($textLesson) ? $textLesson->study_time : '' }}" placeholder="{{ trans('forms.maximum_50_characters') }}"/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.image') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button type="button" class="input-group-text panel-file-manager" data-input="image{{ !empty($textLesson) ? $textLesson->id :'record' }}" data-preview="holder">
                                        <i data-feather="arrow-up" width="18" height="18" class="text-white"></i>
                                    </button>
                                </div>
                                <input type="text" name="ajax[{{ !empty($textLesson) ? $textLesson->id : 'new' }}][image]" id="image{{ !empty($textLesson) ? $textLesson->id :'record' }}" value="{{ !empty($textLesson) ? $textLesson->image : '' }}" class="js-ajax-image form-control"/>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.accessibility') }}</label>

                            <div class="d-flex align-items-center js-ajax-accessibility">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="ajax[{{ !empty($textLesson) ? $textLesson->id : 'new' }}][accessibility]" value="free" @if(empty($textLesson) or (!empty($textLesson) and $textLesson->accessibility == 'free')) checked="checked" @endif id="accessibilityRadio1_{{ !empty($textLesson) ? $textLesson->id : 'record' }}" class="custom-control-input">
                                    <label class="custom-control-label font-14 cursor-pointer" for="accessibilityRadio1_{{ !empty($textLesson) ? $textLesson->id : 'record' }}">{{ trans('public.free') }}</label>
                                </div>

                                <div class="custom-control custom-radio ml-15">
                                    <input type="radio" name="ajax[{{ !empty($textLesson) ? $textLesson->id : 'new' }}][accessibility]" value="paid" @if(!empty($textLesson) and $textLesson->accessibility == 'paid') checked="checked" @endif id="accessibilityRadio2_{{ !empty($textLesson) ? $textLesson->id : 'record' }}" class="custom-control-input">
                                    <label class="custom-control-label font-14 cursor-pointer" for="accessibilityRadio2_{{ !empty($textLesson) ? $textLesson->id : 'record' }}">{{ trans('public.paid') }}</label>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label d-block">{{ trans('public.attachments') }}</label>

                            <select class="js-ajax-attachments @if(empty($textLesson)) form-control @endif attachments-select2" name="ajax[{{ !empty($textLesson) ? $textLesson->id : 'new' }}][attachments]" data-placeholder="{{ trans('public.choose_attachments') }}">
                                <option></option>

                                @if(!empty($webinar->files) and count($webinar->files))
                                    @foreach($webinar->files as $filesInfo)
                                        @if($filesInfo->downloadable)
                                            <option value="{{ $filesInfo->id }}" @if(!empty($textLesson) and in_array($filesInfo->id,$textLesson->attachments->pluck('file_id')->toArray())) selected @endif>{{ $filesInfo->title }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.summary') }}</label>
                            <textarea name="ajax[{{ !empty($textLesson) ? $textLesson->id : 'new' }}][summary]" class="js-ajax-summary form-control" rows="6">{{ !empty($textLesson) ? $textLesson->summary : '' }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label class="input-label">{{ trans('public.content') }}</label>
                            <div class="content-summernote js-ajax-file_path">
                                <textarea class="js-content-summernote form-control">{{ !empty($textLesson) ? $textLesson->content : '' }}</textarea>
                                <textarea name="ajax[{{ !empty($textLesson) ? $textLesson->id : 'new' }}][content]"  class="js-hidden-content-summernote d-none">{{ !empty($textLesson) ? $textLesson->content : '' }}</textarea>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-30 d-flex align-items-center">
                    <button type="button" class="js-save-text_lesson btn btn-sm btn-primary">{{ trans('public.save') }}</button>

                    @if(empty($textLesson))
                        <button type="button" class="btn btn-sm btn-danger ml-10 cancel-accordion">{{ trans('public.close') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</li>
