@push('styles_top')
    <link href="/assets/default/vendors/sortable/jquery-ui.min.css"/>
@endpush


<section class="mt-50">
    <div class="">
        <h2 class="section-title after-line">{{ trans('public.faq') }} ({{ trans('public.optional') }})</h2>
    </div>

    <button id="webinarAddFAQ" data-webinar-id="{{ $webinar->id }}" type="button" class="btn btn-primary btn-sm mt-15">{{ trans('public.add_faq') }}</button>

    <div class="row mt-10">
        <div class="col-12">

            <div class="accordion-content-wrapper mt-15" id="faqsAccordion" role="tablist" aria-multiselectable="true">
                @if(!empty($webinar->faqs) and count($webinar->faqs))
                    <ul class="draggable-lists" data-order-table="faqs">
                        @foreach($webinar->faqs as $faqInfo)
                            @include('web.default.panel.webinar.create_includes.accordions.faq',['webinar' => $webinar,'faq' => $faqInfo])
                        @endforeach
                    </ul>
                @else
                    @include(getTemplate() . '.includes.no-result',[
                        'file_name' => 'faq.png',
                        'title' => trans('public.faq_no_result'),
                        'hint' => trans('public.faq_no_result_hint'),
                    ])
                @endif
            </div>
        </div>
    </div>
</section>

<div id="newFaqForm" class="d-none">
    @include('web.default.panel.webinar.create_includes.accordions.faq',['webinar' => $webinar])
</div>


@push('scripts_bottom')
    <script src="/assets/default/vendors/sortable/jquery-ui.min.js"></script>
@endpush