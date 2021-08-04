@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.promotion_sales') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/admin/">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.promotion_sales') }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>{{ trans('admin/main.title') }}</th>
                                        <th class="text-left">{{ trans('admin/main.full_name') }}</th>
                                        <th class="text-left">{{ trans('admin/main.webinar') }}</th>
                                        <th class="text-center">{{ trans('admin/main.created_at') }}</th>
                                    </tr>

                                    @foreach($promotionSales as $promotionSale)
                                        <tr>
                                            <td class="text-center">{{ $promotionSale->promotion->title }}</td>
                                            <td class="text-left">{{ $promotionSale->buyer->full_name }}</td>
                                            <td class="text-left">
                                                <a href="{{ $promotionSale->webinar->getUrl() }}" target="_blank">{{ $promotionSale->webinar->title }}</a>
                                            </td>
                                            <td class="text-center">{{ dateTimeFormat($promotionSale->created_at, 'Y-m-d H:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $promotionSales->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

