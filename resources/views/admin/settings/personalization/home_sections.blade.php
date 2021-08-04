<div class="tab-pane mt-3 fade " id="home_sections" role="tabpanel" aria-labelledby="home_sections-tab">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <form action="/admin/settings/home_sections" method="post">
                        {{ csrf_field() }}

                        <input type="hidden" name="page" value="personalization">

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div id="addAccountTypes" class="ml-3">

                                    @php
                                        $sections = [
                                                'latest_classes',
                                                'best_sellers',
                                                'free_classes',
                                                'discount_classes',
                                                'best_rates',
                                                'trend_categories',
                                                'testimonials',
                                                'subscribes',
                                                'blog',
                                                'organizations',
                                                'instructors',
                                                'video_or_image_section',
                                        ];
                                    @endphp

                                    @foreach($sections as $section)
                                        <div class="form-group custom-switches-stacked">
                                            <label class="custom-switch pl-0">
                                                <input type="hidden" name="value[{{ $section }}]" value="0">
                                                <input type="checkbox" name="value[{{ $section }}]" id="{{ $section }}Switch" value="1" {{ (!empty($itemValue) and !empty($itemValue[$section]) and $itemValue[$section]) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                                                <span class="custom-switch-indicator"></span>
                                                <label class="custom-switch-description mb-0 cursor-pointer" for="{{ $section }}Switch">{{ trans('admin/main.'.$section) }}</label>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">{{ trans('admin/main.save_change') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
