@extends(getTemplate().'.layouts.app')


@section('content')
    <div class="container">

        <div class="row login-container">
            <div class="col-12 col-md-6 pl-0">
                <img src="{{ getPageBackgroundSettings('become_instructor') }}" class="img-cover" alt="Login">
            </div>

            <div class="col-12 col-md-6">
                <div class="login-card">
                    <h1 class="font-20 font-weight-bold">{{ trans('site.become_instructor') }}</h1>

                    <form method="Post" action="/become_instructor" class="mt-35">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label class="font-weight-500 text-dark-blue">{{ trans('site.occupations') }}</label>

                            <div class="d-flex flex-wrap mt-5">

                                @foreach($categories as $category)
                                    @if(!empty($category->subCategories) and count($category->subCategories))
                                        @foreach($category->subCategories as $subCategory)
                                            <div class="checkbox-button mr-15 mt-10 font-14">
                                                <input type="checkbox" name="occupations[]" id="checkbox{{ $subCategory->id }}" value="{{ $subCategory->id }}" @if(!empty($occupations) and in_array($subCategory->id,$occupations)) checked="checked" @endif>
                                                <label for="checkbox{{ $subCategory->id }}">{{ $subCategory->title }}</label>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="checkbox-button mr-15 mt-10 font-14">
                                            <input type="checkbox" name="occupations[]" id="checkbox{{ $category->id }}" value="{{ $category->id }}" @if(!empty($occupations) and in_array($category->id,$occupations)) checked="checked" @endif>
                                            <label for="checkbox{{ $category->id }}">{{ $category->title }}</label>
                                        </div>
                                    @endif
                                @endforeach

                                @if($errors->has('occupations'))
                                    <div class="text-danger font-14">{{ $errors->first('occupations') }}</div>
                                @endif
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
                                <input type="text" name="certificate" id="certificate" value="{{ !empty($lastRequest) ? $lastRequest->certificate : old('certificate') }}" class="form-control "/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('financial.select_account_type') }}</label>
                            <select name="account_type" class="form-control @error('account_type')  is-invalid @enderror">
                                <option selected disabled>{{ trans('financial.select_account_type') }}</option>

                                @if(!empty(getOfflineBanksTitle()) and count(getOfflineBanksTitle()))
                                    @foreach(getOfflineBanksTitle() as $accountType)
                                        <option value="{{ $accountType }}" @if(!empty($user) and $user->account_type == $accountType) selected="selected" @endif>{{ $accountType }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('account_type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('financial.iban') }}</label>
                            <input type="text" name="iban" value="{{ (!empty($user)) ? $user->iban : old('iban') }}" class="form-control @error('iban')  is-invalid @enderror" placeholder=""/>
                            @error('iban')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('financial.account_id') }}</label>
                            <input type="text" name="account_id" value="{{ (!empty($user)) ? $user->account_id : old('account_id') }}" class="form-control @error('account_id')  is-invalid @enderror" placeholder=""/>
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
                                <input type="text" name="identity_scan" id="identity_scan" value="{{ (!empty($user)) ? $user->identity_scan : old('identity_scan') }}" class="form-control @error('identity_scan')  is-invalid @enderror"/>
                                @error('identity_scan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('site.extra_information') }}</label>
                            <textarea name="description" rows="6" class="form-control">{{ !empty($lastRequest) ? $lastRequest->description : old('description') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-20">{{ trans('site.send_request') }}</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>

    <script src="/assets/default/js/parts/become_instructor.min.js"></script>
@endpush
