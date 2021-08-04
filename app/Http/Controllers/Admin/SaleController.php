<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\Order;
use App\Models\Sale;
use App\Models\SaleLog;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_sales_list');

        $query = Sale::query();

        $totalSales = [
            'count' => deepClone($query)->count(),
            'amount' => deepClone($query)->sum('total_amount'),
        ];

        $classesSales = [
            'count' => deepClone($query)->whereNotNull('webinar_id')->count(),
            'amount' => deepClone($query)->whereNotNull('webinar_id')->sum('total_amount'),
        ];
        $appointmentSales = [
            'count' => deepClone($query)->whereNotNull('meeting_id')->count(),
            'amount' => deepClone($query)->whereNotNull('meeting_id')->sum('total_amount'),
        ];
        $failedSales = Order::where('status', Order::$fail)->count();

        $salesQuery = $this->getSalesFilters($query, $request);

        $sales = $salesQuery->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach ($sales as $sale) {
            $sale = $this->makeTitle($sale);

            if (empty($sale->saleLog)) {
                SaleLog::create([
                    'sale_id' => $sale->id,
                    'viewed_at' => time()
                ]);
            }
        }

        $data = [
            'pageTitle' => trans('admin/pages/financial.sales_page_title'),
            'sales' => $sales,
            'totalSales' => $totalSales,
            'classesSales' => $classesSales,
            'appointmentSales' => $appointmentSales,
            'failedSales' => $failedSales,
        ];

        $teacher_ids = $request->get('teacher_ids');
        $student_ids = $request->get('student_ids');
        $webinar_ids = $request->get('webinar_ids');

        if (!empty($teacher_ids)) {
            $data['teachers'] = User::select('id', 'full_name')
                ->whereIn('id', $teacher_ids)->get();
        }

        if (!empty($student_ids)) {
            $data['students'] = User::select('id', 'full_name')
                ->whereIn('id', $student_ids)->get();
        }

        if (!empty($webinar_ids)) {
            $data['webinars'] = Webinar::select('id', 'title')
                ->whereIn('id', $webinar_ids)->get();
        }

        return view('admin.financial.sales.lists', $data);
    }

    private function makeTitle($sale)
    {
        if (!empty($sale->webinar_id)) {
            $sale->item_title = $sale->webinar->title;
            $sale->item_id = $sale->webinar_id;
            $sale->item_seller = $sale->webinar->creator->full_name;
            $sale->seller_id = $sale->webinar->creator->id;
            $sale->sale_type = $sale->webinar->creator->id;
        } elseif (!empty($sale->meeting_id)) {
            $sale->item_title = trans('panel.meeting');
            $sale->item_id = $sale->meeting_id;
            $sale->item_seller = $sale->meeting->creator->full_name;
            $sale->seller_id = $sale->meeting->creator->id;
        } elseif (!empty($sale->subscribe_id) and !empty($sale->subscribe)) {
            $sale->item_title = $sale->subscribe->title;
            $sale->item_id = $sale->subscribe_id;
            $sale->item_seller = 'Admin';
            $sale->seller_id = '';
        } elseif (!empty($sale->promotion_id) and !empty($sale->promotion)) {
            $sale->item_title = $sale->promotion->title;
            $sale->item_id = $sale->promotion_id;
            $sale->item_seller = 'Admin';
            $sale->seller_id = '';
        } else {
            $sale->item_title = '---';
            $sale->item_id = '---';
            $sale->item_seller = '---';
            $sale->seller_id = '';
        }

        return $sale;
    }

    private function getSalesFilters($query, $request)
    {
        $item_title = $request->get('item_title');
        $from = $request->get('from');
        $to = $request->get('to');
        $status = $request->get('status');
        $webinar_ids = $request->get('webinar_ids', []);
        $teacher_ids = $request->get('teacher_ids', []);
        $student_ids = $request->get('student_ids', []);
        $userIds = array_merge($teacher_ids, $student_ids);

        if (!empty($item_title)) {
            $ids = Webinar::where('title', 'like', "%$item_title%")->pluck('id')->toArray();
            $webinar_ids = array_merge($webinar_ids, $ids);
        }

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($status)) {
            if ($status == 'success') {
                $query->whereNull('refund_at');
            } elseif ($status == 'refund') {
                $query->whereNotNull('refund_at');
            }
        }

        if (!empty($webinar_ids) and count($webinar_ids)) {
            $query->whereIn('webinar_id', $webinar_ids);
        }

        if (!empty($userIds) and count($userIds)) {
            $query->where(function ($query) use ($userIds) {
                $query->whereIn('buyer_id', $userIds);
                $query->orWhereIn('seller_id', $userIds);
            });
        }

        return $query;
    }

    public function refund($id)
    {
        $this->authorize('admin_sales_refund');

        $sale = Sale::findOrFail($id);

        if (!empty($sale->amount) or $sale->amount > 0 or !empty($sale->order_id)) {
            $order = Order::findOrFail($sale->order_id);

            Accounting::refundAccounting($order);
        }


        $sale->update(['refund_at' => time()]);

        return back();
    }

    public function invoice($id)
    {
        $this->authorize('admin_sales_invoice');

        $sale = Sale::where('id', $id)
            ->with([
                'order',

                'buyer' => function ($query) {
                    $query->select('id', 'full_name');
                },
                'webinar' => function ($query) {
                    $query->with([
                        'teacher' => function ($query) {
                            $query->select('id', 'full_name');
                        },
                        'creator' => function ($query) {
                            $query->select('id', 'full_name');
                        },
                        'webinarPartnerTeacher' => function ($query) {
                            $query->with([
                                'teacher' => function ($query) {
                                    $query->select('id', 'full_name');
                                },
                            ]);
                        }
                    ]);
                }
            ])
            ->first();

        if (!empty($sale)) {
            $webinar = $sale->webinar;

            if (!empty($webinar)) {
                $data = [
                    'pageTitle' => trans('webinars.invoice_page_title'),
                    'sale' => $sale,
                    'webinar' => $webinar
                ];

                return view('admin.financial.sales.invoice', $data);
            }
        }

        abort(404);
    }

    public function exportExcel()
    {
        $this->authorize('admin_sales_export');

    }
}
