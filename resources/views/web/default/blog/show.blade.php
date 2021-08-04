@extends(getTemplate().'.layouts.app')

@section('content')
    <section class="cart-banner position-relative text-center">
        <div class="container h-100">
            <div class="row h-100 align-items-center justify-content-center text-center">
                <div class="col-12 col-md-9 col-lg-7">

                    <h1 class="font-30 text-white font-weight-bold">{{ $post->title }}</h1>

                    <div class="d-flex flex-column flex-sm-row align-items-center align-sm-items-start justify-content-between">
                        <span class="mt-10 mt-md-20 font-16 font-weight-500 text-white">{{ trans('public.created_by') }}
                            <span class="text-white text-decoration-underline">{{ $post->author->full_name }}</span>
                        </span>
                        <span class="mt-10 mt-md-20 font-16 font-weight-500 text-white">{{ trans('public.in') }}
                            <a href="{{ $post->category->getUrl() }}" class="text-white text-decoration-underline">{{ $post->category->title }}</a>
                        </span>
                        <span class="mt-10 mt-md-20 font-16 font-weight-500 text-white">{{ dateTimeFormat($post->created_at, 'j F Y') }}</span>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="container mt-10 mt-md-40">
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="post-show mt-30">

                    <div class="post-img pb-30">
                        <img src="{{ $post->image }}" alt="">
                    </div>

                    {!! nl2br(clean($post->content)) !!}
                </div>

                {{-- post Comments --}}
                @include('web.default.includes.comments',[
                        'comments' => $post->comments,
                        'inputName' => 'blog_id',
                        'inputValue' => $post->id
                    ])
                {{-- ./ post Comments --}}

            </div>
            <div class="col-12 col-lg-4">

                <div class="rounded-lg shadow-sm mt-35 p-20 course-teacher-card d-flex align-items-center flex-column">
                    <div class="teacher-avatar mt-5">
                        <img src="{{ $post->author->getAvatar() }}" class="img-cover" alt="">
                    </div>
                    <h3 class="mt-10 font-20 font-weight-bold text-secondary">{{ $post->author->full_name }}</h3>
                    <span class="mt-5 font-weight-500 font-14 text-gray">{{ $post->author->role->caption }}</span>

                    <div class="mt-25 d-flex align-items-center  w-100">
                        <a href="/blog?author={{ $post->author->id }}" class="btn btn-sm btn-primary btn-block px-15">{{ trans('public.author_posts') }}</a>
                    </div>
                </div>

                {{-- categories --}}
                <div class="p-20 mt-30 rounded-sm shadow-lg border border-gray300">
                    <h3 class="category-filter-title font-16 font-weight-bold text-dark-blue">{{ trans('categories.categories') }}</h3>

                    <div class="pt-15">
                        @foreach($blogCategories as $blogCategory)
                            <a href="{{ $blogCategory->getUrl() }}" class="font-14 text-dark-blue d-block mt-15">{{ $blogCategory->title }}</a>
                        @endforeach
                    </div>
                </div>

                {{-- recent_posts --}}
                <div class="p-20 mt-30 rounded-sm shadow-lg border border-gray300">
                    <h3 class="category-filter-title font-20 font-weight-bold text-dark-blue">{{ trans('site.recent_posts') }}</h3>

                    <div class="pt-15">

                        @foreach($popularPosts as $popularPost)
                            <div class="popular-post d-flex align-items-start mt-20">
                                <div class="popular-post-image rounded">
                                    <img src="{{ $popularPost->image }}" class="img-cover rounded" alt="{{ $popularPost->title }}">
                                </div>
                                <div class="popular-post-content d-flex flex-column ml-10">
                                    <a href="{{ $popularPost->getUrl() }}">
                                        <h3 class="font-14 text-dark-blue">{{ truncate($popularPost->title,40) }}</h3>
                                    </a>
                                    <span class="mt-auto font-12 text-gray">{{ dateTimeFormat($popularPost->created_at, 'Y/m/d') }}</span>
                                </div>
                            </div>
                        @endforeach

                        <a href="/blog" class="btn btn-sm btn-primary btn-block mt-30">{{ trans('home.view_all') }} {{ trans('site.posts') }}</a>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script>
        ;(function (){ 
        'use strict'
        var webinarDemoLang = '{{ trans('webinars.webinar_demo') }}';
        var replyLang = '{{ trans('panel.reply') }}';
        var closeLang = '{{ trans('public.close') }}';
        var saveLang = '{{ trans('public.save') }}';
        var reportLang = '{{ trans('panel.report') }}';
        var reportSuccessLang = '{{ trans('panel.report_success') }}';
        var messageToReviewerLang = '{{ trans('public.message_to_reviewer') }}';
        }())
    </script>

    <script src="/assets/default/js/parts/comment.min.js"></script>
@endpush
