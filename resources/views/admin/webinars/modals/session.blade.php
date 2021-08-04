<!-- Modal -->
<div class="d-none" id="webinarSessionModal">
    <h3 class="section-title after-line font-20 text-dark-blue mb-25">{{ trans('public.add_session') }}</h3>

    <form action="/admin/sessions/store" method="post" class="session-form">
        <input type="hidden" name="webinar_id" value="{{ !empty($webinar) ? $webinar->id :''  }}">

        <div class="form-group">
            <label class="input-label">{{ trans('webinars.select_session_api') }}</label>

            <div class="js-session-api">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" name="session_api" id="localApi_record" value="local" checked class="js-api-input custom-control-input">
                    <label class="custom-control-label" for="localApi_record">{{ trans('webinars.session_local_api') }}</label>
                </div>

                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" name="session_api" id="bigBlueButton_record" value="big_blue_button" class="js-api-input custom-control-input">
                    <label class="custom-control-label" for="bigBlueButton_record">{{ trans('webinars.session_big_blue_button') }}</label>
                </div>

                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" name="session_api" id="zoomApi_record" value="zoom" class="js-api-input custom-control-input">
                    <label class="custom-control-label" for="zoomApi_record">{{ trans('webinars.session_zoom') }}</label>
                </div>
            </div>

            <div class="invalid-feedback"></div>

            <div class="js-zoom-not-complete-alert mt-10 text-danger d-none">
                {{ trans('admin/main.teacher_zoom_jwt_token_invalid') }}
            </div>
        </div>


        <div class="form-group js-api-secret">
            <label class="input-label">{{ trans('auth.password') }}</label>
            <input type="text" name="api_secret" class="js-ajax-api_secret form-control" value=""/>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group js-moderator-secret d-none">
            <label class="input-label">{{ trans('public.moderator_password') }}</label>
            <input type="text" name="moderator_secret" class="js-ajax-moderator_secret form-control" value="" />
            <div class="invalid-feedback"></div>
        </div>


        <div class="form-group">
            <label class="input-label">{{ trans('public.title') }}</label>
            <input type="text" name="title" class="form-control" placeholder="{{ trans('forms.maximum_50_characters') }}"/>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.date') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="dateRangeLabel">
                        <i class="fa fa-calendar"></i>
                    </span>
                </div>
                <input type="text" name="date" class="js-ajax-date form-control datetimepicker" aria-describedby="dateRangeLabel"/>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.duration') }} <span class="braces">({{ trans('public.minutes') }})</span></label>
            <input type="text" name="duration" class="js-ajax-duration form-control" placeholder=""/>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group js-local-link">
            <label class="input-label">{{ trans('public.link') }}</label>
            <input type="text" name="link" class="js-ajax-link form-control" placeholder=""/>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.description') }}</label>
            <textarea name="description" class="form-control" rows="6"></textarea>
            <div class="invalid-feedback"></div>
        </div>

        <div class="mt-30 d-flex align-items-center justify-content-end">
            <button type="button" id="saveSession" class="btn btn-primary">{{ trans('public.save') }}</button>
            <button type="button" class="btn btn-danger ml-2 close-swl">{{ trans('public.close') }}</button>
        </div>
    </form>
</div>
