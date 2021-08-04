@extends(getTemplate().'.layouts.app')

@section('content')
    <div class="container">
        <div class="row login-container">
            <div class="col-12 col-md-6 pl-0">
                <img src="{{ getPageBackgroundSettings('remember_pass') }}" class="img-cover" alt="Login">
            </div>

            <div class="col-12 col-md-6">

                <div class="login-card">
                    <h1 class="font-20 font-weight-bold">{{ trans('auth.forget_password') }}</h1>

                    <form method="post" action="/send-email" class="mt-35">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label class="input-label" for="email">{{ trans('auth.email') }}:</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                   aria-describedby="emailHelp">
                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-20">{{ trans('auth.reset_password') }}</button>
                    </form>

                   <div class="text-center mt-20">
                        <span class="badge badge-circle-gray300 text-secondary d-inline-flex align-items-center justify-content-center">or</span>
                    </div>

                    <div class="text-center mt-20">
                        <span class="text-secondary">
                            <a href="/login" class="text-secondary font-weight-bold">{{ trans('auth.login') }}</a>
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
