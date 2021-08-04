<div class="tab-pane mt-3 fade @if(empty(request()->get('page'))) active show @endif" id="basic" role="tabpanel" aria-labelledby="basic-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="/admin/settings/main" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="page" value="financial">
                <input type="hidden" name="name" value="financial">


                <div class="form-group">
                    <label>{{ trans('admin/main.default_commission') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                        <input type="number" name="value[commission]" value="{{ (!empty($itemValue) and !empty($itemValue['commission'])) ? $itemValue['commission'] : old('commission') }}" class="form-control text-center" maxlength="3" min="0" max="100"/>
                    </div>
                    <div class="text-muted text-small mt-1">{{ trans('admin/main.default_commission_hint') }}</div>
                </div>


                <div class="form-group">
                    <label>{{ trans('admin/main.tax') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                        <input type="number" name="value[tax]" value="{{ (!empty($itemValue) and !empty($itemValue['tax'])) ? $itemValue['tax'] : old('tax') }}" class="form-control text-center" maxlength="3" min="0" max="100"/>
                    </div>
                </div>


                <div class="form-group">
                    <label>{{ trans('admin/main.minimum_payout_amount') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <input type="number" name="value[minimum_payout]" value="{{ (!empty($itemValue) and !empty($itemValue['minimum_payout'])) ? $itemValue['minimum_payout'] : old('minimum_payout') }}" class="form-control text-center" min="0"/>
                    </div>
                    <div class="text-muted text-small mt-1">{{ trans('admin/main.minimum_payout_amount_hint') }}</div>
                </div>


                <div class="form-group">
                    <label class="input-label d-block">{{ trans('admin/main.currency') }}</label>
                    <select name="value[currency]" class="form-control select2" data-placeholder="{{ trans('admin/main.currency') }}">
                        <option value=""></option>
                        @foreach(currenciesLists() as $key => $currency)
                            <option value="{{ $key }}" @if((!empty($itemValue) and !empty($itemValue['currency'])) and $itemValue['currency'] == $key) selected @endif >{{ $currency }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-success">{{ trans('admin/main.save_change') }}</button>
            </form>
        </div>
    </div>
</div>
