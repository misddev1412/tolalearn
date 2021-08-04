@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/bootstrap-timepicker/bootstrap-timepicker.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/bootstrap-tagsinput/bootstrap-tagsinput.min.css">
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
    <style>
        .bootstrap-timepicker-widget table td input {
            width: 35px !important;
        }
    </style>
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{!empty($webinar) ?trans('/admin/main.edit'): trans('admin/main.new') }} {{ trans('admin/main.class') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/admin/">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item active">
                    <a href="/admin/webinars">{{ trans('admin/main.classes') }}</a>
                </div>
                <div class="breadcrumb-item">{{!empty($webinar) ?trans('/admin/main.edit'): trans('admin/main.new') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 ">
                    <div class="card">
                        <div class="card-body">

                            <form method="post" action="/admin/webinars/{{ !empty($webinar) ? $webinar->id.'/update' : 'store' }}" id="webinarForm" class="webinar-form">
                                {{ csrf_field() }}
                                <section>
                                    <h2 class="section-title after-line">{{ trans('public.basic_information') }}</h2>

                                    <div class="row">
                                        <div class="col-12 col-md-5">

                                            <div class="form-group mt-15 ">
                                                <label class="input-label d-block">{{ trans('panel.course_type') }}</label>

                                                <select name="type" class="custom-select @error('type')  is-invalid @enderror">
                                                    <option value="webinar" @if((!empty($webinar) and $webinar->isWebinar()) or old('type') == \App\Models\Webinar::$webinar) selected @endif>{{ trans('webinars.webinar') }}</option>
                                                    <option value="course" @if((!empty($webinar) and $webinar->isCourse()) or old('type') == \App\Models\Webinar::$course) selected @endif>{{ trans('product.video_course') }}</option>
                                                    <option>{{ trans('product.text_course') }} (Paid Plugin)</option>

                                                </select>

                                                @error('type')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('public.title') }}</label>
                                                <input type="text" name="title" value="{{ !empty($webinar) ? $webinar->title : old('title') }}" class="form-control @error('title')  is-invalid @enderror" placeholder="{{ trans('forms.maximum_50_characters') }}"/>
                                                @error('title')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>


                                            @if(!empty($webinar) and $webinar->creator->isOrganization())
                                                <div class="form-group mt-15 ">
                                                    <label class="input-label d-block">{{ trans('admin/main.organization') }}</label>

                                                    <select class="form-control" disabled readonly data-placeholder="{{ trans('public.search_instructor') }}">
                                                        <option selected>{{ $webinar->creator->full_name }}</option>
                                                    </select>
                                                </div>
                                            @endif


                                            <div class="form-group mt-15 ">
                                                <label class="input-label d-block">{{ trans('admin/main.select_a_instructor') }}</label>

                                                <select name="teacher_id" class="form-control select2 @error('teacher_id')  is-invalid @enderror" data-placeholder="{{ trans('public.search_instructor') }}">
                                                    <option {{ !empty($webinar) ? '' : 'selected' }} disabled>{{ trans('public.select_a_teacher') }}</option>
                                                    @foreach($teachers as $teacher)
                                                        <option value="{{ $teacher->id }}" @if(!empty($webinar) and $webinar->teacher_id == $teacher->id) selected @endif>{{ $teacher->full_name }}</option>
                                                    @endforeach
                                                </select>

                                                @error('teacher_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>


                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('public.seo_description') }}</label>
                                                <input type="text" name="seo_description" value="{{ !empty($webinar) ? $webinar->seo_description : old('seo_description') }}" class="form-control @error('seo_description')  is-invalid @enderror"/>
                                                @error('seo_description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                <div class="text-muted text-small mt-1">{{ trans('admin/main.seo_description_hint') }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('public.thumbnail_image') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <button type="button" class="input-group-text admin-file-manager" data-input="thumbnail" data-preview="holder">
                                                            <i class="fa fa-upload"></i>
                                                        </button>
                                                    </div>
                                                    <input type="text" name="thumbnail" id="thumbnail" value="{{ !empty($webinar) ? $webinar->thumbnail : old('thumbnail') }}" class="form-control @error('thumbnail')  is-invalid @enderror"/>
                                                    <div class="input-group-append">
                                                        <button type="button" class="input-group-text admin-file-view" data-input="thumbnail">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    </div>
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
                                                        <button type="button" class="input-group-text admin-file-manager" data-input="cover_image" data-preview="holder">
                                                            <i class="fa fa-upload"></i>
                                                        </button>
                                                    </div>
                                                    <input type="text" name="image_cover" id="cover_image" value="{{ !empty($webinar) ? $webinar->image_cover : old('image_cover') }}" class="form-control @error('image_cover')  is-invalid @enderror"/>
                                                    <div class="input-group-append">
                                                        <button type="button" class="input-group-text admin-file-view" data-input="cover_image">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    </div>
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
                                                        <button type="button" class="input-group-text admin-file-manager" data-input="demo_video" data-preview="holder">
                                                            <i class="fa fa-upload"></i>
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
                                </section>

                                <section class="mt-3">
                                    <h2 class="section-title after-line">{{ trans('public.additional_information') }}</h2>
                                    <div class="row">
                                        <div class="col-12 col-md-6">

                                            @if(empty($webinar) or (!empty($webinar) and $webinar->isWebinar()))

                                                <div class="form-group mt-15 js-capacity {{ (!empty(old('type')) and old('type') != \App\Models\Webinar::$webinar) ? 'd-none' : '' }}">
                                                    <label class="input-label">{{ trans('public.capacity') }}</label>
                                                    <input type="text" name="capacity" value="{{ !empty($webinar) ? $webinar->capacity : old('capacity') }}" class="form-control @error('capacity')  is-invalid @enderror"/>
                                                    @error('capacity')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            @endif

                                            <div class="row mt-15">
                                                @if(empty($webinar) or (!empty($webinar) and $webinar->isWebinar()))
                                                    <div class="col-12 col-md-6 js-start_date {{ (!empty(old('type')) and old('type') != \App\Models\Webinar::$webinar) ? 'd-none' : '' }}">
                                                        <div class="form-group">
                                                            <label class="input-label">{{ trans('public.start_date') }}</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="dateInputGroupPrepend">
                                                                        <i class="fa fa-calendar-alt "></i>
                                                                    </span>
                                                                </div>
                                                                <input type="text" name="start_date" value="{{ (!empty($webinar) and $webinar->start_date) ? dateTimeFormat($webinar->start_date,'Y-m-d') : old('start_date') }}" class="form-control @error('start_date')  is-invalid @enderror datepicker" aria-describedby="dateInputGroupPrepend"/>
                                                                @error('start_date')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label class="input-label">{{ trans('public.duration') }} ({{ trans('public.minutes') }})</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="timeInputGroupPrepend">
                                                                    <i class="fa fa-clock"></i>
                                                                </span>
                                                            </div>


                                                            <input type="text" name="duration" value="{{ !empty($webinar) ? $webinar->duration : old('duration') }}" class="form-control @error('duration')  is-invalid @enderror"/>
                                                            @error('duration')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if(!empty($webinar) and $webinar->creator->isOrganization())
                                                <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                                                    <label class="" for="privateSwitch">{{ trans('webinars.private') }}</label>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" name="private" class="custom-control-input" id="privateSwitch" {{ (!empty($webinar) and $webinar->private) ? 'checked' :  '' }}>
                                                        <label class="custom-control-label" for="privateSwitch"></label>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                                                <label class="" for="supportSwitch">{{ trans('panel.support') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="support" class="custom-control-input" id="supportSwitch" {{ !empty($webinar) && $webinar->support ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="supportSwitch"></label>
                                                </div>
                                            </div>

                                            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                                                <label class="cursor-pointer" for="downloadableSwitch">{{ trans('home.downloadable') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="downloadable" class="custom-control-input" id="downloadableSwitch" {{ !empty($webinar) && $webinar->downloadable ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="downloadableSwitch"></label>
                                                </div>
                                            </div>

                                            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                                                <label class="" for="partnerInstructorSwitch">{{ trans('public.partner_instructor') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="partner_instructor" class="custom-control-input" id="partnerInstructorSwitch" {{ !empty($webinar) && $webinar->partner_instructor ? 'checked' : ''  }}>
                                                    <label class="custom-control-label" for="partnerInstructorSwitch"></label>
                                                </div>
                                            </div>

                                            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                                                <label class="" for="subscribeSwitch">{{ trans('public.subscribe') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="subscribe" class="custom-control-input" id="subscribeSwitch" {{ !empty($webinar) && $webinar->subscribe ? 'checked' : ''  }}>
                                                    <label class="custom-control-label" for="subscribeSwitch"></label>
                                                </div>
                                            </div>

                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('public.price') }}</label>
                                                <input type="text" name="price" value="{{ !empty($webinar) ? $webinar->price : old('price') }}" class="form-control @error('price')  is-invalid @enderror" placeholder="{{ trans('public.0_for_free') }}"/>
                                                @error('price')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div id="partnerInstructorInput" class="form-group mt-15 {{ !empty($webinar) && $webinar->partner_instructor ? '' : 'd-none' }}">
                                                <label class="input-label d-block">{{ trans('public.select_a_partner_teacher') }}</label>

                                                <select name="partners[]" class="form-control select2" multiple="" data-placeholder="{{ trans('public.search_instructor') }}">
                                                    @foreach($teachers as $teacher)
                                                        <option value="{{ $teacher->id }}" {{ !empty($webinar) && isset($webinarPartnerTeacher[$teacher->id]) ? 'selected' : '' }}>{{ $teacher->full_name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="text-muted text-small mt-1">{{ trans('admin/main.select_a_partner_hint') }}</div>
                                            </div>

                                            <div class="form-group mt-15">
                                                <label class="input-label d-block">{{ trans('public.tags') }}</label>
                                                <input type="text" name="tags" data-max-tag="5" value="{{ !empty($webinar) ? implode(',',$webinarTags) : '' }}" class="form-control inputtags" placeholder="{{ trans('public.type_tag_name_and_press_enter') }} ({{ trans('admin/main.max') }} : 5)"/>
                                            </div>


                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('public.category') }}</label>

                                                <select id="categories" class="custom-select @error('category_id')  is-invalid @enderror" name="category_id" required>
                                                    <option {{ !empty($webinar) ? '' : 'selected' }} disabled>{{ trans('public.choose_category') }}</option>
                                                    @foreach($categories as $category)
                                                        @if(!empty($category->subCategories) and count($category->subCategories))
                                                            <optgroup label="{{  $category->title }}">
                                                                @foreach($category->subCategories as $subCategory)
                                                                    <option value="{{ $subCategory->id }}" {{ (!empty($webinar) and $webinar->category_id == $subCategory->id) ? 'selected' : '' }}>{{ $subCategory->title }}</option>
                                                                @endforeach
                                                            </optgroup>
                                                        @else
                                                            <option value="{{ $category->id }}" {{ (!empty($webinar) and $webinar->category_id == $category->id) ? 'selected' : '' }}>{{ $category->title }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>

                                                @error('category_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group mt-15 {{ (!empty($webinarCategoryFilters) and count($webinarCategoryFilters)) ? '' : 'd-none' }}" id="categoriesFiltersContainer">
                                        <span class="input-label d-block">{{ trans('public.category_filters') }}</span>
                                        <div id="categoriesFiltersCard" class="row mt-3">

                                            @if(!empty($webinarCategoryFilters) and count($webinarCategoryFilters))
                                                @foreach($webinarCategoryFilters as $filter)
                                                    <div class="col-12 col-md-3">
                                                        <div class="webinar-category-filters">
                                                            <strong class="category-filter-title d-block">{{ $filter->title }}</strong>
                                                            <div class="py-10"></div>

                                                            @foreach($filter->options as $option)
                                                                <div class="form-group mt-3 d-flex align-items-center justify-content-between">
                                                                    <label class="text-gray font-14" for="filterOptions{{ $option->id }}">{{ $option->title }}</label>
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="filters[]" value="{{ $option->id }}" {{ ((!empty($webinarFilterOptions) && in_array($option->id,$webinarFilterOptions)) ? 'checked' : '') }} class="custom-control-input" id="filterOptions{{ $option->id }}">
                                                                        <label class="custom-control-label" for="filterOptions{{ $option->id }}"></label>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif

                                        </div>
                                    </div>
                                </section>

                                @if(!empty($webinar))
                                    <section class="mt-30">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h2 class="section-title after-line">{{ trans('admin/main.price_plans') }}</h2>
                                            <button id="webinarAddTicket" type="button" class="btn btn-primary btn-sm mt-3">{{ trans('admin/main.add_price_plan') }}</button>
                                        </div>

                                        <div class="row mt-10">
                                            <div class="col-12">

                                                @if(!empty($tickets) and !$tickets->isEmpty())
                                                    <div class="table-responsive">
                                                        <table class="table table-striped text-center font-14">

                                                            <tr>
                                                                <th>{{ trans('public.title') }}</th>
                                                                <th>{{ trans('public.discount') }}</th>
                                                                <th>{{ trans('public.capacity') }}</th>
                                                                <th>{{ trans('public.date') }}</th>
                                                                <th></th>
                                                            </tr>

                                                            @foreach($tickets as $ticket)
                                                                <tr>
                                                                    <th scope="row">{{ $ticket->title }}</th>
                                                                    <td>{{ $ticket->discount }}%</td>
                                                                    <td>{{ $ticket->capacity }}</td>
                                                                    <td>{{ dateTimeFormat($ticket->start_date,'j F Y') }} - {{ (new DateTime())->setTimestamp($ticket->end_date)->format('j F Y') }}</td>
                                                                    <td>
                                                                        <button type="button" data-ticket-id="{{ $ticket->id }}" data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}" class="edit-ticket btn-transparent text-primary mt-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                            <i class="fa fa-edit"></i>
                                                                        </button>

                                                                        @include('admin.includes.delete_button',['url' => '/admin/tickets/'. $ticket->id .'/delete', 'btnClass' => ' mt-1'])
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </table>
                                                    </div>
                                                @else
                                                    @include('admin.includes.no-result',[
                                                        'file_name' => 'ticket.png',
                                                        'title' => trans('public.ticket_no_result'),
                                                        'hint' => trans('public.ticket_no_result_hint'),
                                                    ])
                                                @endif
                                            </div>
                                        </div>
                                    </section>

                                    @if($webinar->isWebinar())
                                        <section class="mt-30">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h2 class="section-title after-line">{{ trans('public.sessions') }}</h2>
                                                <button id="webinarAddSession" type="button" class="btn btn-primary btn-sm mt-3">{{ trans('public.add_session') }}</button>
                                            </div>

                                            <div class="row mt-10">
                                                <div class="col-12">
                                                    @if(!empty($sessions) and !$sessions->isEmpty())
                                                        <div class="table-responsive">
                                                            <table class="table table-striped text-center font-14">

                                                                <tr>
                                                                    <th>{{ trans('public.title') }}</th>
                                                                    <th>{{ trans('admin/main.session_api') }}</th>
                                                                    <th>{{ trans('public.date_time') }}</th>
                                                                    <th>{{ trans('public.duration') }}</th>
                                                                    <th width="20%">{{ trans('public.link') }}</th>
                                                                    <th></th>
                                                                </tr>

                                                                @foreach($sessions as $session)
                                                                    <tr>
                                                                        <th>{{ $session->title }}</th>
                                                                        <th>{{ trans('webinars.session_'.$session->session_api) }}</th>
                                                                        <td>{{ dateTimeFormat($session->date,'j F Y \|\ H:i') }}</td>
                                                                        <td>{{ $session->duration }} {{ trans('admin/main.min') }}</td>
                                                                        <td>{{ $session->getJoinLink() }}</td>
                                                                        <td>
                                                                            <button type="button" data-session-id="{{ $session->id }}" data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}" class="edit-session btn-transparent text-primary mt-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                                <i class="fa fa-edit"></i>
                                                                            </button>

                                                                            @include('admin.includes.delete_button',['url' => '/admin/sessions/'. $session->id .'/delete', 'btnClass' => ' mt-1'])
                                                                        </td>
                                                                    </tr>
                                                                @endforeach

                                                            </table>
                                                        </div>
                                                    @else
                                                        @include('admin.includes.no-result',[
                                                            'file_name' => 'meet.png',
                                                            'title' => trans('public.sessions_no_result'),
                                                            'hint' => trans('public.sessions_no_result_hint'),
                                                        ])
                                                    @endif
                                                </div>
                                            </div>
                                        </section>
                                    @endif

                                    <section class="mt-30">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h2 class="section-title after-line">{{ trans('public.files') }}</h2>
                                            <button id="webinarAddFile" type="button" class="btn btn-primary btn-sm mt-3">{{ trans('public.add_files') }}</button>
                                        </div>

                                        <div class="row mt-10">
                                            <div class="col-12">
                                                @if(!empty($files) and !$files->isEmpty())
                                                    <div class="table-responsive">
                                                        <table class="table table-striped text-center font-14">

                                                            <tr>
                                                                <th>{{ trans('public.title') }}</th>
                                                                <th>{{ trans('public.source') }}</th>
                                                                <th>{{ trans('public.volume') }}</th>
                                                                <th>{{ trans('public.file_type') }}</th>
                                                                <th>{{ trans('public.accessibility') }}</th>
                                                                <th></th>
                                                            </tr>

                                                            @foreach($files as $file)
                                                                <tr>
                                                                    <th>{{ $file->title }}</th>
                                                                    <td>{{ $file->storage }}</td>
                                                                    <td>{{ $file->volume }}</td>
                                                                    <td>{{ $file->file_type }}</td>
                                                                    <td>{{ $file->accessibility }}</td>

                                                                    <td>
                                                                        <button type="button" data-file-id="{{ $file->id }}" data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}" class="edit-file btn-transparent text-primary mt-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                            <i class="fa fa-edit"></i>
                                                                        </button>

                                                                        @include('admin.includes.delete_button',['url' => '/admin/files/'. $file->id .'/delete', 'btnClass' => ' mt-1'])
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </table>
                                                    </div>
                                                @else
                                                    @include('admin.includes.no-result',[
                                                        'file_name' => 'files.png',
                                                        'title' => trans('public.files_no_result'),
                                                        'hint' => trans('public.files_no_result_hint'),
                                                    ])
                                                @endif
                                            </div>
                                        </div>
                                    </section>

                                    @if($webinar->isTextCourse())
                                        <section class="mt-30">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h2 class="section-title after-line">{{ trans('public.test_lesson') }}</h2>
                                                <button id="webinarAddTestLesson" type="button" class="btn btn-primary btn-sm mt-3">{{ trans('public.add_test_lesson') }}</button>
                                            </div>

                                            <div class="row mt-10">
                                                <div class="col-12">
                                                    @if(!empty($textLessons) and !$textLessons->isEmpty())
                                                        <div class="table-responsive">
                                                            <table class="table table-striped text-center font-14">

                                                                <tr>
                                                                    <th>{{ trans('public.title') }}</th>
                                                                    <th>{{ trans('public.time') }}</th>
                                                                    <th>{{ trans('public.attachments') }}</th>
                                                                    <th>{{ trans('public.accessibility') }}</th>
                                                                    <th></th>
                                                                </tr>

                                                                @foreach($textLessons as $textLesson)
                                                                    <tr>
                                                                        <th>{{ $textLesson->title }}</th>
                                                                        <td>{{ $textLesson->study_time }}</td>
                                                                        <td>
                                                                            @if(count($textLesson->attachments) > 0)
                                                                                <span class="text-success">{{ trans('admin/main.yes') }} ({{ count($textLesson->attachments) }})</span>
                                                                            @else
                                                                                <span class="text-dark">{{ trans('admin/main.no') }}</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $textLesson->accessibility }}</td>

                                                                        <td>
                                                                            <button type="button" data-text-id="{{ $textLesson->id }}" data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}" class="edit-test-lesson btn-transparent text-primary mt-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                                <i class="fa fa-edit"></i>
                                                                            </button>

                                                                            @include('admin.includes.delete_button',['url' => '/admin/test-lesson/'. $textLesson->id .'/delete', 'btnClass' => ' mt-1'])
                                                                        </td>
                                                                    </tr>
                                                                @endforeach

                                                            </table>
                                                        </div>
                                                    @else
                                                        @include('admin.includes.no-result',[
                                                            'file_name' => 'files.png',
                                                            'title' => trans('public.files_no_result'),
                                                            'hint' => trans('public.files_no_result_hint'),
                                                        ])
                                                    @endif
                                                </div>
                                            </div>
                                        </section>
                                    @endif

                                    <section class="mt-30">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h2 class="section-title after-line">{{ trans('public.prerequisites') }}</h2>
                                            <button id="webinarAddPrerequisites" type="button" class="btn btn-primary btn-sm mt-3">{{ trans('public.add_prerequisites') }}</button>
                                        </div>

                                        <div class="row mt-10">
                                            <div class="col-12">
                                                @if(!empty($prerequisites) and !$prerequisites->isEmpty())
                                                    <div class="table-responsive">
                                                        <table class="table table-striped text-center font-14">

                                                            <tr>
                                                                <th>{{ trans('public.title') }}</th>
                                                                <th class="text-left">{{ trans('public.instructor') }}</th>
                                                                <th>{{ trans('public.price') }}</th>
                                                                <th>{{ trans('public.publish_date') }}</th>
                                                                <th>{{ trans('public.forced') }}</th>
                                                                <th></th>
                                                            </tr>

                                                            @foreach($prerequisites as $prerequisite)
                                                                <tr>
                                                                    <th>{{ $prerequisite->prerequisiteWebinar->title }}</th>
                                                                    <td class="text-left">{{ $prerequisite->prerequisiteWebinar->teacher->full_name }}</td>
                                                                    <td>$ {{  number_format($prerequisite->prerequisiteWebinar->price,2) }}</td>
                                                                    <td>{{ dateTimeFormat($prerequisite->prerequisiteWebinar->created_at,'j F Y | H:i') }}</td>
                                                                    <td>{{ $prerequisite->required ? trans('public.yes') : trans('public.no') }}</td>

                                                                    <td>
                                                                        <button type="button" data-prerequisite-id="{{ $prerequisite->id }}" data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}" class="edit-prerequisite btn-transparent text-primary mt-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                            <i class="fa fa-edit"></i>
                                                                        </button>

                                                                        @include('admin.includes.delete_button',['url' => '/admin/prerequisites/'. $prerequisite->id .'/delete', 'btnClass' => ' mt-1'])
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </table>
                                                    </div>
                                                @else
                                                    @include('admin.includes.no-result',[
                                                        'file_name' => 'comment.png',
                                                        'title' => trans('public.prerequisites_no_result'),
                                                        'hint' => trans('public.prerequisites_no_result_hint'),
                                                    ])
                                                @endif
                                            </div>
                                        </div>
                                    </section>

                                    <section class="mt-30">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h2 class="section-title after-line">{{ trans('public.faq') }}</h2>
                                            <button id="webinarAddFAQ" type="button" class="btn btn-primary btn-sm mt-3">{{ trans('public.add_faq') }}</button>
                                        </div>

                                        <div class="row mt-10">
                                            <div class="col-12">
                                                @if(!empty($faqs) and !$faqs->isEmpty())
                                                    <div class="table-responsive">
                                                        <table class="table table-striped text-center font-14">

                                                            <tr>
                                                                <th>{{ trans('public.title') }}</th>
                                                                <th>{{ trans('public.answer') }}</th>
                                                                <th></th>
                                                            </tr>

                                                            @foreach($faqs as $faq)
                                                                <tr>
                                                                    <th>{{ $faq->title }}</th>
                                                                    <td>
                                                                        <button type="button" data-action="/admin/faqs/{{ $faq->id }}/description" class="get-description btn btn-sm btn-gray200">{{ trans('public.view') }}</button>
                                                                    </td>

                                                                    <td>
                                                                        <button type="button" data-faq-id="{{ $faq->id }}" data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}" class="edit-faq btn-transparent text-primary mt-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                            <i class="fa fa-edit"></i>
                                                                        </button>

                                                                        @include('admin.includes.delete_button',['url' => '/admin/faqs/'. $faq->id .'/delete', 'btnClass' => ' mt-1'])
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </table>
                                                    </div>
                                                @else
                                                    @include('admin.includes.no-result',[
                                                        'file_name' => 'faq.png',
                                                        'title' => trans('public.faq_no_result'),
                                                        'hint' => trans('public.faq_no_result_hint'),
                                                    ])
                                                @endif
                                            </div>
                                        </div>
                                    </section>

                                    <section class="mt-30">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h2 class="section-title after-line">{{ trans('public.quiz_certificate') }}</h2>
                                            <button id="webinarAddQuiz" type="button" class="btn btn-primary btn-sm mt-3">{{ trans('public.add_quiz') }}</button>
                                        </div>
                                        <div class="row mt-10">
                                            <div class="col-12">
                                                @if(!empty($webinarQuizzes) and !$webinarQuizzes->isEmpty())
                                                    <div class="table-responsive">
                                                        <table class="table table-striped text-center font-14">

                                                            <tr>
                                                                <th>{{ trans('public.title') }}</th>
                                                                <th>{{ trans('public.questions') }}</th>
                                                                <th>{{ trans('public.total_mark') }}</th>
                                                                <th>{{ trans('public.pass_mark') }}</th>
                                                                <th>{{ trans('public.certificate') }}</th>
                                                                <th></th>
                                                            </tr>

                                                            @foreach($webinarQuizzes as $webinarQuiz)
                                                                <tr>
                                                                    <th>{{ $webinarQuiz->title }}</th>
                                                                    <td>{{ $webinarQuiz->quizQuestions->count() }}</td>
                                                                    <td>{{ $webinarQuiz->quizQuestions->sum('grade') }}</td>
                                                                    <td>{{ $webinarQuiz->pass_mark }}</td>
                                                                    <td>{{ $webinarQuiz->certificate ? trans('public.yes') : trans('public.no') }}</td>
                                                                    <td>
                                                                        <button type="button" data-webinar-quiz-id="{{ $webinarQuiz->id }}" data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}" class="edit-webinar-quiz btn-transparent text-primary mt-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                            <i class="fa fa-edit"></i>
                                                                        </button>

                                                                        @include('admin.includes.delete_button',['url' => '/admin/webinar-quiz/'. $webinarQuiz->id .'/delete', 'btnClass' => ' mt-1'])
                                                                    </td>
                                                                    @endforeach
                                                                </tr>

                                                        </table>
                                                    </div>
                                                @else
                                                    @include('admin.includes.no-result',[
                                                        'file_name' => 'cert.png',
                                                        'title' => trans('public.quizzes_no_result'),
                                                        'hint' => trans('public.quizzes_no_result_hint'),
                                                    ])
                                                @endif
                                            </div>
                                        </div>
                                    </section>
                                @endif

                                <section class="mt-3">
                                    <h2 class="section-title after-line">{{ trans('public.message_to_reviewer') }}</h2>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group mt-15">
                                                <textarea name="message_for_reviewer" rows="10" class="form-control">{{ !empty($webinar) && $webinar->message_for_reviewer ? $webinar->message_for_reviewer : old('message_for_reviewer') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <input type="hidden" name="draft" value="no" id="forDraft"/>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" id="saveAndPublish" class="btn btn-success">{{ !empty($webinar) ? trans('admin/main.save_and_publish') : trans('admin/main.save_and_continue') }}</button>

                                        @if(!empty($webinar))
                                            <button type="button" id="saveReject" class="btn btn-warning">{{ trans('public.reject') }}</button>

                                            @include('admin.includes.delete_button',[
                                                    'url' => '/admin/webinars/'. $webinar->id .'/delete',
                                                    'btnText' => trans('public.delete'),
                                                    'hideDefaultClass' => true,
                                                    'btnClass' => 'btn btn-danger'
                                                    ])
                                        @endif
                                    </div>
                                </div>
                            </form>


                            @include('admin.webinars.modals.prerequisites')
                            @include('admin.webinars.modals.quizzes')
                            @include('admin.webinars.modals.ticket')
                            @include('admin.webinars.modals.session')
                            @include('admin.webinars.modals.file')
                            @include('admin.webinars.modals.faq')
                            @include('admin.webinars.modals.testLesson')

                        </div>
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
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        var zoomJwtTokenInvalid = '{{ trans('admin/main.teacher_zoom_jwt_token_invalid') }}';
        }())
    </script>

    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/vendors/moment.min.js"></script>
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/assets/default/vendors/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
    <script src="/assets/default/vendors/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
    <script src="/assets/admin/js/webinar.min.js"></script>
@endpush
