@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.quizzes') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/admin/">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.quizzes') }}</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ trans('admin/main.total_quizzes') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalQuizzes }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-clipboard-check"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ trans('admin/main.active_quizzes') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalActiveQuizzes }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-users"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ trans('admin/main.total_students') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalStudents }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-user-check"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ trans('admin/main.total_passed_students') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalPassedStudents }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-body">

            <section class="card">
                <div class="card-body">
                    <form action="/admin/quizzes" method="get" class="row mb-0">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.search') }}</label>
                                <input type="text" class="form-control" name="title" value="{{ request()->get('title') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.start_date') }}</label>
                                <div class="input-group">
                                    <input type="date" id="fsdate" class="text-center form-control" name="from" value="{{ request()->get('from') }}" placeholder="Start Date">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.end_date') }}</label>
                                <div class="input-group">
                                    <input type="date" id="lsdate" class="text-center form-control" name="to" value="{{ request()->get('to') }}" placeholder="End Date">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.filters') }}</label>
                                <select name="sort" data-plugin-selectTwo class="form-control populate">
                                    <option value="">{{ trans('admin/main.filter_type') }}</option>
                                    <option value="have_certificate" @if(request()->get('sort') == 'have_certificate') selected @endif>{{ trans('admin/main.quizzes_have_certificate') }}</option>
                                    <option value="students_count_asc" @if(request()->get('sort') == 'students_count_asc') selected @endif>{{ trans('admin/main.students_ascending') }}</option>
                                    <option value="students_count_desc" @if(request()->get('sort') == 'students_count_desc') selected @endif>{{ trans('admin/main.students_descending') }}</option>
                                    <option value="passed_count_asc" @if(request()->get('sort') == 'passed_count_asc') selected @endif>{{ trans('admin/main.passed_students_ascending') }}</option>
                                    <option value="passed_count_desc" @if(request()->get('sort') == 'passed_count_desc') selected @endif>{{ trans('admin/main.passes_students_descending') }}</option>
                                    <option value="grade_avg_asc" @if(request()->get('sort') == 'grade_avg_asc') selected @endif>{{ trans('admin/main.grades_average_ascending') }}</option>
                                    <option value="grade_avg_desc" @if(request()->get('sort') == 'grade_avg_desc') selected @endif>{{ trans('admin/main.grades_average_descending') }}</option>
                                    <option value="created_at_asc" @if(request()->get('sort') == 'created_at_asc') selected @endif>{{ trans('admin/main.create_date_ascending') }}</option>
                                    <option value="created_at_desc" @if(request()->get('sort') == 'created_at_desc') selected @endif>{{ trans('admin/main.create_date_descending') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.instructor') }}</label>
                                <select name="teacher_ids[]" multiple="multiple" data-search-option="just_teacher_role" class="form-control search-user-select2"
                                        data-placeholder="Search teachers">

                                    @if(!empty($teachers) and $teachers->count() > 0)
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" selected>{{ $teacher->full_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.class') }}</label>
                                <select name="webinar_ids[]" multiple="multiple" class="form-control search-webinar-select2"
                                        data-placeholder="Search classes">

                                    @if(!empty($webinars) and $webinars->count() > 0)
                                        @foreach($webinars as $webinar)
                                            <option value="{{ $webinar->id }}" selected>{{ $webinar->title }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.status') }}</label>
                                <select name="statue" data-plugin-selectTwo class="form-control populate">
                                    <option value="">{{ trans('admin/main.all_status') }}</option>
                                    <option value="active" @if(request()->get('status') == 'active') selected @endif>{{ trans('admin/main.active') }}</option>
                                    <option value="inactive" @if(request()->get('status') == 'inactive') selected @endif>{{ trans('admin/main.inactive') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-3 d-flex align-items-center justify-content-end">
                            <button type="submit" class="btn btn-primary w-100">{{ trans('admin/main.show_results') }}</button>
                        </div>
                    </form>
                </div>
            </section>

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            @can('admin_quizzes_lists_excel')
                                <div class="text-right">
                                    <a href="/admin/quizzes/excel?{{ http_build_query(request()->all()) }}" class="btn btn-primary">{{ trans('admin/main.export_xls') }}</a>
                                </div>
                            @endcan
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th class="text-left">{{ trans('admin/main.title') }}</th>
                                        <th class="text-left">{{ trans('admin/main.instructor') }}</th>
                                        <th class="text-center">{{ trans('admin/main.question_count') }}</th>
                                        <th class="text-center">{{ trans('admin/main.students_count') }}</th>
                                        <th class="text-center">{{ trans('admin/main.average_grade') }}</th>
                                        <th class="text-center">{{ trans('admin/main.certificate') }}</th>
                                        <th class="text-center">{{ trans('admin/main.status') }}</th>
                                        <th>{{ trans('admin/main.actions') }}</th>
                                    </tr>

                                    @foreach($quizzes as $quiz)
                                        <tr>
                                            <td>
                                                <span>{{ $quiz->title }}</span>
                                                @if(!empty($quiz->webinar))
                                                    <small class="d-block text-left text-primary">{{ $quiz->webinar->title }}</small>
                                                @endif
                                            </td>

                                            <td class="text-left">{{ $quiz->teacher->full_name }}</td>

                                            <td class="text-center">
                                                {{ $quiz->quizQuestions->count() }}
                                            </td>

                                            <td class="text-center">
                                                <span>{{ $quiz->quizResults->pluck('user_id')->count() }}</span>
                                                <span class="d-block text-primary font-12">({{ trans('admin/main.passed') }}: {{ $quiz->quizResults->where('status','passed')->count() }})</span>
                                            </td>

                                            <td class="text-center">{{ round($quiz->quizResults->avg('user_grade'),2) }} </td>

                                            <td class="text-center">
                                                @if($quiz->certificate)
                                                    <a class="text-success fas fa-check"></a>
                                                @else
                                                    <a class="text-danger fas fa-times"></a>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @if($quiz->status === \App\Models\Quiz::ACTIVE)
                                                    <span class="text-success">{{ trans('admin/main.active') }}</span>
                                                @else
                                                    <span class="text-warning">{{ trans('admin/main.inactive') }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                @can('admin_quizzes_results')
                                                    <a href="/admin/quizzes/{{ $quiz->id }}/results" class="btn-transparent btn-sm text-primary" data-toggle="tooltip" title="{{ trans('admin/main.quiz_results') }}">
                                                        <i class="fa fa-poll fa-1x"></i>
                                                    </a>
                                                @endcan

                                                @can('admin_quizzes_edit')
                                                    <a href="/admin/quizzes/{{ $quiz->id }}/edit" class="btn-transparent btn-sm text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('admin_quizzes_delete')
                                                    @include('admin.includes.delete_button',['url' => '/admin/quizzes/'.$quiz->id.'/delete' , 'btnClass' => 'btn-sm'])
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach

                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $quizzes->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
@endpush
