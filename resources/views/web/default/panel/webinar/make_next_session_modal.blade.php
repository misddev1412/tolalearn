<div class="d-none" id="webinarNextSessionModal">
    <form action="/panel/sessions/store" method="post">
        {{ csrf_field() }}

        <input type="hidden" name="ajax[new][webinar_id]">

        <h3 class="section-title after-line font-16 text-dark-blue mb-25">{{ trans('webinars.next_session_info') }}</h3>

        <div class="mt-25">
            <div class="row">
                <div class="col-12 col-md-7">
                    <div class="form-group">
                        <label class="input-label">{{ trans('webinars.session_title') }}</label>
                        <input type="text" name="ajax[new][title]" class="js-ajax-title form-control" value=""/>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="col-12 col-md-5">
                    <div class="form-group">
                        <label class="input-label">{{ trans('public.date') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                            </span>
                            </div>
                            <input type="text" name="ajax[new][date]" value="" class="js-ajax-date form-control datetimepicker"/>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label class="input-label">{{ trans('public.description') }}</label>
                        <textarea name="ajax[new][description]" class="js-ajax-description form-control" rows="5"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="section-title after-line font-16 text-dark-blue mb-25">{{ trans('webinars.join_information') }}</h3>

        <div class="row">
            <div class="col-6 js-local-link">
                <div class="form-group">
                    <label class="input-label">{{ trans('public.link') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text js-copy" data-input="ajax[new][link]" data-toggle="tooltip" data-placement="top" title="{{ trans('public.copy') }}" data-copy-text="{{ trans('public.copy') }}" data-done-text="{{ trans('public.copied') }}">
                                <i data-feather="copy" width="18" height="18" class="text-white"></i>
                            </button>
                        </div>
                        <input type="text" name="ajax[new][link]" value="" class="js-ajax-link form-control"/>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="form-group">
                    <label class="input-label">{{ trans('public.duration') }}</label>
                    <input type="text" name="ajax[new][duration]" value="" class="js-ajax-duration form-control"/>
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label class="input-label">{{ trans('webinars.system') }}</label>

                    <select name="ajax[new][session_api]" class="js-ajax-session_api form-control">
                        <option value="local">{{ trans('webinars.session_local_api') }}</option>
                        <option value="big_blue_button">{{ trans('webinars.session_big_blue_button') }}</option>
                        <option value="zoom">{{ trans('webinars.session_zoom') }}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            <div class="col-12 col-md-6 js-api-secret">
                <div class="form-group">
                    <label class="input-label">{{ trans('auth.password') }}</label>
                    <input type="text" name="ajax[new][api_secret]" class="js-ajax-api_secret form-control" value=""/>
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            <div class="col-12 col-md-6 js-moderator-secret d-none">
                <div class="form-group">
                    <label class="input-label">{{ trans('public.moderator_password') }}</label>
                    <input type="text" name="ajax[new][moderator_secret]" class="js-ajax-moderator_secret form-control" value=""/>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        </div>

        <div class="mt-30 d-flex align-items-center justify-content-end">
            <button type="button" class="js-save-next-session btn btn-sm btn-primary">{{ trans('public.save') }}</button>
            <button type="button" class="btn btn-sm btn-danger ml-10 close-swl">{{ trans('public.close') }}</button>
        </div>
    </form>
</div>
