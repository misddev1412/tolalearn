@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')

@endpush
@section('content')
    @include('web.default.panel.quizzes.create_quiz_form')
@endsection

@push('scripts_bottom')
    <script>
        ;(function (){ 
        'use strict'
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        }())
    </script>
    <script src="/assets/default/js/panel/quiz.min.js"></script>
@endpush
