<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ trans('auth.admin_login_title') }}</title>

    <!-- General CSS File -->
    <link rel="stylesheet" href="/assets/admin/vendor/bootstrap/bootstrap.min.css"/>
    <link rel="stylesheet" href="/assets/admin/vendor/fontawesome/css/all.min.css"/>
    <link rel="stylesheet" href="/assets/admin/css/style.css">
    <link rel="stylesheet" href="/assets/admin/css/components.css">
</head>
<body>

<div id="app">
    @php
        $siteGeneralSettings = getGeneralSettings();
        $getPageBackgroundSettings = getPageBackgroundSettings();

    @endphp

    <section class="section">
        <div class="d-flex flex-wrap align-items-stretch">
            <div class="col-lg-4 col-md-6 col-12 order-lg-1 min-vh-100 order-2 bg-white">
                <div class="p-4 m-3">
                    <img src="{{ $siteGeneralSettings['logo'] ?? '' }}" alt="logo" width="40%" class="mb-5 mt-2">

                    <h4 class="text-dark font-weight-normal">{{ trans('auth.welcome') }} <span class="font-weight-bold">{{ $siteGeneralSettings['site_name'] ?? '' }} {{ trans('auth.admin_panel') }}</span></h4>

                    <p class="text-muted">{{ trans('auth.admin_tagline') }}</p>

                    <form method="POST" action="{{url('/admin/login')}}" class="needs-validation" novalidate="">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label for="email">{{ trans('auth.email') }}</label>
                            <input id="email" type="email" value="{{ old('email') }}" class="form-control  @error('email')  is-invalid @enderror"
                                   name="email" tabindex="1"
                                   required autofocus>
                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="d-block">
                                <label for="password" class="control-label">{{ trans('auth.password') }}</label>
                            </div>
                            <input id="password" type="password" class="form-control  @error('password')  is-invalid @enderror"
                                   name="password" tabindex="2" required>
                            @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="remember" class="custom-control-input" tabindex="3"
                                       id="remember-me">
                                <label class="custom-control-label"
                                       for="remember-me">{{ trans('auth.remember_me') }}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                {{ trans('auth.login') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-8 col-12 order-lg-2 order-1 min-vh-100 background-walk-y position-relative overlay-gradient-bottom" data-background="{{ $getPageBackgroundSettings['admin_login'] ?? '' }}">
                <div class="absolute-bottom-left index-2">
                    <div class="text-light p-5 pb-2">
                        <div class="mb-5 pb-3">
                            <h1 class="mb-2 display-4 font-weight-bold"></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- General JS Scripts -->
<script src="/assets/admin/vendor/jquery/jquery-3.3.1.min.js"></script>
<script src="/assets/admin/vendor/poper/popper.min.js"></script>
<script src="/assets/admin/vendor/bootstrap/bootstrap.min.js"></script>
<script src="/assets/admin/vendor/nicescroll/jquery.nicescroll.min.js"></script>
<script src="/assets/admin/vendor/moment/moment.min.js"></script>
<script src="/assets/admin/js/stisla.js"></script>


<!-- Template JS File -->
<script src="/assets/admin/js/scripts.js"></script>
<script src="/assets/admin/js/custom.js"></script>

</body>
</html>
