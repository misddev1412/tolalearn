<li data-id="{{ !empty($session) ? $session->id :'' }}" class="accordion-row bg-white rounded-sm panel-shadow mt-20 py-15 py-lg-30 px-10 px-lg-20">
    <div class="d-flex align-items-center justify-content-between " role="tab" id="session_{{ !empty($session) ? $session->id :'record' }}">
        <div class="font-weight-bold text-dark-blue" href="#collapseSession{{ !empty($session) ? $session->id :'record' }}" aria-controls="collapseSession{{ !empty($session) ? $session->id :'record' }}" data-parent="#sessionsAccordion" role="button" data-toggle="collapse" aria-expanded="true">
            <span>{{ !empty($session) ? $session->title : trans('public.add_new_sessions') }}</span>
        </div>

        <div class="d-flex align-items-center">
            <i data-feather="move" class="move-icon mr-10 cursor-pointer" height="20"></i>

            @if(!empty($session))
                <div class="btn-group dropdown table-actions mr-15">
                    <button type="button" class="btn-transparent dropdown-toggle d-flex align-items-center justify-content-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i data-feather="more-vertical" height="20"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="/panel/sessions/{{ $session->id }}/delete" class="delete-action btn btn-sm btn-transparent">{{ trans('public.delete') }}</a>
                    </div>
                </div>
            @endif

            <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseSession{{ !empty($session) ? $session->id :'record' }}" aria-controls="collapseSession{{ !empty($session) ? $session->id :'record' }}" data-parent="#sessionsAccordion" role="button" data-toggle="collapse" aria-expanded="true"></i>
        </div>
    </div>

    <div id="collapseSession{{ !empty($session) ? $session->id :'record' }}" aria-labelledby="session_{{ !empty($session) ? $session->id :'record' }}" class=" collapse @if(empty($session)) show @endif" role="tabpanel">
        <div class="panel-collapse text-gray">
            <div class="session-form" data-action="/panel/sessions/{{ !empty($session) ? $session->id . '/update' : 'store' }}">
                <input type="hidden" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][webinar_id]" value="{{ !empty($webinar) ? $webinar->id :'' }}">

                <div class="row">
                    <div class="col-12 col-lg-6">

                        <div class="form-group">
                            <label class="input-label">{{ trans('webinars.select_session_api') }}</label>

                            <div class="js-session-api">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][session_api]" id="localApi{{ !empty($session) ? $session->id : '' }}" value="local" @if(empty($session) or $session->session_api == 'local') checked @endif class="js-api-input custom-control-input" {{ (!empty($session) and $session->session_api != 'local') ? 'disabled' :'' }}>
                                    <label class="custom-control-label" for="localApi{{ !empty($session) ? $session->id : '' }}">{{ trans('webinars.session_local_api') }}</label>
                                </div>

                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][session_api]" id="bigBlueButton{{ !empty($session) ? $session->id : '' }}" value="big_blue_button" @if(!empty($session) and $session->session_api == 'big_blue_button') checked @endif class="js-api-input custom-control-input" {{ (!empty($session) and $session->session_api != 'local') ? 'disabled' :'' }}>
                                    <label class="custom-control-label" for="bigBlueButton{{ !empty($session) ? $session->id : '' }}">{{ trans('webinars.session_big_blue_button') }}</label>
                                </div>

                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][session_api]" id="zoomApi{{ !empty($session) ? $session->id : '' }}" value="zoom" @if(!empty($session) and $session->session_api == 'zoom') checked @endif class="js-api-input custom-control-input" {{ (!empty($session) and $session->session_api != 'local') ? 'disabled' :'' }}>
                                    <label class="custom-control-label" for="zoomApi{{ !empty($session) ? $session->id : '' }}">{{ trans('webinars.session_zoom') }}</label>
                                </div>
                            </div>

                            <div class="invalid-feedback"></div>

                            <div class="js-zoom-not-complete-alert mt-10 text-danger d-none">
                                {{ trans('webinars.your_zoom_settings_are_not_complete') }}
                                <a href="/panel/setting/step/8" class="text-primary" target="_blank">{{ trans('public.go_to_settings') }}</a>
                            </div>
                        </div>


                        <div class="form-group js-api-secret {{ (!empty($session) and $session->session_api == 'zoom') ? 'd-none' :'' }}">
                            <label class="input-label">{{ trans('auth.password') }}</label>
                            <input type="text" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][api_secret]" class="js-ajax-api_secret form-control" value="{{ !empty($session) ? $session->api_secret : '' }}" {{ (!empty($session) and $session->session_api != 'local') ? 'disabled' :'' }}/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group js-moderator-secret {{ (empty($session) or $session->session_api != 'big_blue_button') ? 'd-none' :'' }}">
                            <label class="input-label">{{ trans('public.moderator_password') }}</label>
                            <input type="text" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][moderator_secret]" class="js-ajax-moderator_secret form-control" value="{{ !empty($session) ? $session->moderator_secret : '' }}" {{ (!empty($session) and $session->session_api == 'big_blue_button') ? 'disabled' :'' }}/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.title') }}</label>
                            <input type="text" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][title]" class="js-ajax-title form-control" value="{{ !empty($session) ? $session->title : '' }}" placeholder="{{ trans('forms.maximum_50_characters') }}"/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.date') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="dateRangeLabel">
                                        <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                                    </span>
                                </div>
                                <input type="text" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][date]" class="js-ajax-date form-control datetimepicker" value="{{ !empty($session) ? dateTimeFormat($session->date, 'Y-m-d H:i') : '' }}" aria-describedby="dateRangeLabel" {{ (!empty($session) and $session->session_api != 'local') ? 'disabled' :'' }}/>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.duration') }} <span class="braces">({{ trans('public.minutes') }})</span></label>
                            <input type="text" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][duration]" class="js-ajax-duration form-control" value="{{ !empty($session) ? $session->duration : '' }}" {{ (!empty($session) and $session->session_api != 'local') ? 'disabled' :'' }}/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group js-local-link">
                            <label class="input-label">{{ trans('public.link') }}</label>
                            <input type="text" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][link]" class="js-ajax-link form-control" value="{{ !empty($session) ? $session->getJoinLink() : '' }}" {{ (!empty($session) and $session->session_api != 'local') ? 'disabled' :'' }}/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.description') }}</label>
                            <textarea name="ajax[{{ !empty($session) ? $session->id : 'new' }}][description]" class="js-ajax-description form-control" rows="6">{{ !empty($session) ? $session->description : '' }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                    </div>
                </div>

                <div class="mt-30 d-flex align-items-center">
                    <button type="button" class="js-save-session btn btn-sm btn-primary">{{ trans('public.save') }}</button>

                    @if(!empty($session))
                        <a href="{{ $session->getJoinLink(true) }}" target="_blank" class="ml-10 btn btn-sm btn-secondary">{{ trans('footer.join') }}</a>
                    @endif

                    @if(empty($session))
                        <button type="button" class="btn btn-sm btn-danger ml-10 cancel-accordion">{{ trans('public.close') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</li>
