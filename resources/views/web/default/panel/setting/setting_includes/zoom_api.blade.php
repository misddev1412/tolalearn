<section class="mt-30">
    <h2 class="section-title after-line">{{ trans('public.zoom_api') }}</h2>

    <div class="row mt-20">
        <div class="col-12 col-lg-4">

            <div class="form-group">
                <label class="input-label">{{ trans('public.zoom_jwt_token') }}</label>
                <textarea type="text" name="zoom_jwt_token" rows="6" class="form-control @error('zoom_jwt_token')  is-invalid @enderror">{{ (!empty($user) and empty($new_user) and $user->zoomApi) ? $user->zoomApi->jwt_token : old('zoom_jwt_token') }}</textarea>
                @error('zoom_jwt_token')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

        </div>
    </div>
        <div>
        <p class="font-12 text-gray"><a href="https://marketplace.zoom.us/docs/guides/auth/jwt">{{ trans('public.how_zoom_jwt_token') }}</a></p>
    </div>

</section>
