<div class="tab-pane mt-3 fade" id="home_hero1" role="tabpanel" aria-labelledby="home_hero1-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="/admin/settings/main" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="name" value="home_hero">
                <input type="hidden" name="page" value="personalization">

                <div class="form-group">
                    <label>{{ trans('admin/main.title') }}</label>
                    <input type="text" name="value[title]" value="{{ (!empty($itemValue) and !empty($itemValue['title'])) ? $itemValue['title'] : old('title') }}" class="form-control "/>
                </div>

                <div class="form-group">
                    <label>{{ trans('public.description') }}</label>
                    <textarea type="text" name="value[description]" rows="5" class="form-control ">{{ (!empty($itemValue) and !empty($itemValue['description'])) ? $itemValue['description'] : old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="input-label">{{ trans('admin/main.hero_background') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text admin-file-manager" data-input="hero_background" data-preview="holder">
                                <i class="fa fa-chevron-up"></i>
                            </button>
                        </div>
                        <input type="text" name="value[hero_background]" id="hero_background" value="{{ (!empty($itemValue) and !empty($itemValue['hero_background'])) ? $itemValue['hero_background'] : old('hero_background') }}" class="form-control"/>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">{{ trans('admin/main.save_change') }}</button>
            </form>
        </div>
    </div>
</div>
