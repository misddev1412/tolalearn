@php
    $socials = getSocials();
    if (!empty($socials) and count($socials)) {
        $socials = collect($socials)->sortBy('order')->toArray();
    }

    $footerColumns = getFooterColumns();
@endphp

<footer class="footer bg-secondary position-relative user-select-none">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class=" footer-subscribe d-block d-md-flex align-items-center justify-content-between">
                    <div class="flex-grow-1">
                        <strong>{{ trans('footer.join_us_today') }}</strong>
                        <span class="d-block mt-5 text-white">{{ trans('footer.subscribe_content') }}</span>
                    </div>
                    <div class="subscribe-input bg-white p-10 flex-grow-1 mt-30 mt-md-0">
                        <form action="/newsletters" method="post">
                            {{ csrf_field() }}

                            <div class="form-group d-flex align-items-center m-0">
                                <div class="w-100">
                                    <input type="text" name="newsletter_email" class="form-control border-0 @error('newsletter_email') is-invalid @enderror" placeholder="{{ trans('footer.enter_email_here') }}"/>
                                    @error('newsletter_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary rounded-pill">{{ trans('footer.join') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $columns = ['first_column','second_column','third_column','forth_column'];
    @endphp

    <div class="container">
        <div class="row">

            @foreach($columns as $column)
                <div class="col-6 col-md-3">
                    @if(!empty($footerColumns[$column]))
                        @if(!empty($footerColumns[$column]['title']))
                            <span class="header d-block text-white font-weight-bold">{{ $footerColumns[$column]['title'] }}</span>
                        @endif

                        @if(!empty($footerColumns[$column]['value']))
                            <div class="mt-20">
                                {!! clean($footerColumns[$column]['value']) !!}
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach

        </div>

        <div class="mt-40 border-blue py-25 d-flex align-items-center justify-content-between">
            <div class="footer-logo">
                <a href="/">
                    @if(!empty($generalSettings['footer_logo']))
                        <img src="{{ $generalSettings['footer_logo'] }}" class="img-cover" alt="footer logo">
                    @endif
                </a>
            </div>
            <div class="footer-social">
                @foreach($socials as $social)
                    <a href="{{ $social['link'] }}">
                        <img src="{{ $social['image'] }}" alt="{{ $social['title'] }}" class="mr-15">
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
