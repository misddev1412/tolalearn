@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush

<div class="row">
    <div class="col-12 col-md-4 mt-15">

        <div class="form-group mt-15 ">
            <label class="input-label d-block">{{ trans('panel.course_type') }}</label>

            <select name="type" class="custom-select @error('type')  is-invalid @enderror">
                <option value="webinar" @if(!empty($webinar) and $webinar->isWebinar()) selected @endif>{{ trans('webinars.webinar') }}</option>
                <option value="course" @if(!empty($webinar) and $webinar->type == 'course') selected @endif>{{ trans('webinars.video_course') }}</option>
                <option>{{ trans('webinars.text_lesson') }} (Paid Plugin)</option>
            </select>

            @error('type')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>


        @if($isOrganization)
            <div class="form-group mt-15 ">
                <label class="input-label d-block">{{ trans('public.select_a_teacher') }}</label>

                <select name="teacher_id" class="custom-select @error('teacher_id')  is-invalid @enderror">
                    <option {{ !empty($webinar) ? '' : 'selected' }} disabled>{{ trans('public.choose_instructor') }}</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ !empty($webinar) && $webinar->teacher_id === $teacher->id? 'selected' : '' }}>{{ $teacher->full_name }}</option>
                    @endforeach
                </select>

                @error('teacher_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        @endif


        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.title') }}</label>
            <input type="text" name="title" value="{{ !empty($webinar) ? $webinar->title : old('title') }}" class="form-control @error('title')  is-invalid @enderror" placeholder="{{ trans('forms.maximum_64_characters') }}"/>
            @error('title')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.seo_description') }}</label>
            <input type="text" name="seo_description" value="{{ !empty($webinar) ? $webinar->seo_description : old('seo_description') }}" class="form-control @error('seo_description')  is-invalid @enderror " placeholder="{{ trans('forms.50_160_characters_preferred') }}"/>
            @error('seo_description')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.thumbnail_image') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text panel-file-manager" data-input="thumbnail" data-preview="holder">
                        <i data-feather="arrow-up" width="18" height="18" class="text-white"></i>
                    </button>
                </div>
                <input type="text" name="thumbnail" id="thumbnail" value="{{ !empty($webinar) ? $webinar->thumbnail : old('thumbnail') }}" class="form-control @error('thumbnail')  is-invalid @enderror" placeholder="{{ trans('forms.course_thumbnail_size') }}"/>
                @error('thumbnail')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.cover_image') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text panel-file-manager" data-input="cover_image" data-preview="holder">
                        <i data-feather="arrow-up" width="18" height="18" class="text-white"></i>
                    </button>
                </div>
                <input type="text" name="image_cover" id="cover_image" value="{{ !empty($webinar) ? $webinar->image_cover : old('image_cover') }}" placeholder="{{ trans('forms.course_cover_size') }}" class="form-control @error('image_cover')  is-invalid @enderror"/>
                @error('image_cover')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.demo_video') }} ({{ trans('public.optional') }})</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text panel-file-manager" data-input="demo_video" data-preview="holder">
                        <i data-feather="arrow-up" width="18" height="18" class="text-white"></i>
                    </button>
                </div>
                <input type="text" name="video_demo" id="demo_video" value="{{ !empty($webinar) ? $webinar->video_demo : old('video_demo') }}" class="form-control @error('video_demo')  is-invalid @enderror"/>
                @error('video_demo')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.description') }}</label>
            <textarea id="summernote" name="description" class="form-control @error('description')  is-invalid @enderror" placeholder="{{ trans('forms.webinar_description_placeholder') }}">{{ !empty($webinar) ? $webinar->description : old('description')  }}</textarea>
            @error('description')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
</div>

@if($isOrganization)
    <div class="row">
        <div class="col-6">

            <div class="form-group mt-30 d-flex align-items-center">
                <label class="cursor-pointer mb-0 input-label" for="privateSwitch">{{ trans('webinars.private') }}</label>
                <div class="ml-30 custom-control custom-switch">
                    <input type="checkbox" name="private" class="custom-control-input" id="privateSwitch" {{ (!empty($webinar) and $webinar->private) ? 'checked' :  '' }}>
                    <label class="custom-control-label" for="privateSwitch"></label>
                </div>
            </div>

            <p class="text-gray font-12">{{ trans('webinars.create_private_course_hint') }}</p>
        </div>
    </div>
@endif

@push('scripts_bottom')
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
@endpush
