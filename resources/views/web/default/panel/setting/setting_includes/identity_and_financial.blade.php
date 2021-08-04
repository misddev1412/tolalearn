<section class="mt-30">
    <h2 class="section-title after-line">{{ trans('site.identity_and_financial') }}</h2>
    <div class="mt-15">
        @if($user->financial_approval)
            <p class="font-14 text-primary">{{ trans('site.identity_and_financial_verified') }}</p>
        @else
            <p class="font-14 text-danger">{{ trans('site.identity_and_financial_not_verified') }}</p>
        @endif
    </div>

    <div class="row mt-20">
        <div class="col-12 col-lg-4">

            <div class="form-group">
                <label class="input-label">{{ trans('financial.select_account_type') }}</label>
                <select name="account_type" class="form-control">
                    <option selected disabled>{{ trans('financial.select_account_type') }}</option>
                    @if(!empty(getOfflineBanksTitle()) and count(getOfflineBanksTitle())) {
                        @foreach(getOfflineBanksTitle() as $accountType)
                            <option value="{{ $accountType }}" @if($user->account_type == $accountType) selected @endif>{{ $accountType }}</option>
                        @endforeach
                    @endif
                </select>

                @error('account')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('financial.iban') }}</label>
                <input type="text" name="iban" value="{{ (!empty($user) and empty($new_user)) ? $user->iban : old('iban') }}" class="form-control @error('iban')  is-invalid @enderror" placeholder=""/>
                @error('iban')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('financial.account_id') }}</label>
                <input type="text" name="account_id" value="{{ (!empty($user) and empty($new_user)) ? $user->account_id : old('account_id') }}" class="form-control @error('account_id')  is-invalid @enderror" placeholder=""/>
                @error('account_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('financial.identity_scan') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="button" class="input-group-text panel-file-manager" data-input="identity_scan" data-preview="holder">
                            <i data-feather="arrow-up" width="18" height="18" class="text-white"></i>
                        </button>
                    </div>
                    <input type="text" name="identity_scan" id="identity_scan" value="{{ (!empty($user) and empty($new_user)) ? $user->identity_scan : old('identity_scan') }}" class="form-control @error('identity_scan')  is-invalid @enderror"/>
                    @error('identity_scan')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('public.certificate_and_documents') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="button" class="input-group-text panel-file-manager" data-input="certificate" data-preview="holder">
                            <i data-feather="arrow-up" width="18" height="18" class="text-white"></i>
                        </button>
                    </div>
                    <input type="text" name="certificate" id="certificate" value="{{ (!empty($user) and empty($new_user)) ? $user->certificate : old('certificate') }}" class="form-control "/>
                </div>
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('financial.address') }}</label>
                <input type="text" name="address" value="{{ (!empty($user) and empty($new_user)) ? $user->address : old('address') }}" class="form-control @error('address')  is-invalid @enderror" placeholder=""/>
                @error('address')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

        </div>
    </div>

</section>
