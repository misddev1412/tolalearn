<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\DiscountUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_discount_codes_list');

        $query = Discount::query();

        $query = $this->filters($query, $request);

        $discounts = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.discount_codes_title'),
            'discounts' => $discounts,
        ];

        return view('admin.financial.discount.lists', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $search = $request->get('search');
        $user_ids = $request->get('user_ids', []);
        $sort = $request->get('sort');


        $query = fromAndToDateFilter($from, $to, $query, 'expired_at');


        if (!empty($user_ids) and count($user_ids)) {
            $discountIds = DiscountUser::whereIn('user_id', $user_ids)->pluck('discount_id');

            $query = $query->whereIn('id', $discountIds);
        }

        if (isset($search)) {
            $query = $query->where('name', 'like', '%' . $search . '%');
        }

        if (!empty($sort)) {
            switch ($sort) {
                case 'percent_asc':
                    $query->orderBy('percent', 'asc');
                    break;
                case 'percent_desc':
                    $query->orderBy('percent', 'desc');
                    break;
                case 'amount_asc':
                    $query->orderBy('amount', 'asc');
                    break;
                case 'amount_desc':
                    $query->orderBy('amount', 'desc');
                    break;
                case 'usable_time_asc':
                    $query->orderBy('count', 'asc');
                    break;
                case 'usable_time_desc':
                    $query->orderBy('count', 'desc');
                    break;
                case 'usable_time_remain_asc':
                    $query->leftJoin('order_items', 'discounts.id', '=', 'order_items.discount_id')
                        ->select('discounts.*', 'order_items.order_id', DB::raw('(discounts.count - count(order_items.order_id)) as remain_count'))
                        ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                        ->where(function ($query) {
                            $query->whereNull('order_id')
                                ->orWhere('orders.status', 'paid');
                        })
                        ->groupBy('order_items.order_id')
                        ->orderBy('remain_count', 'asc');
                    break;
                case 'usable_time_remain_desc':
                    $query->leftJoin('order_items', 'discounts.id', '=', 'order_items.discount_id')
                        ->select('discounts.*', 'order_items.order_id', DB::raw('(discounts.count - count(order_items.order_id)) as remain_count'))
                        ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
                        ->where(function ($query) {
                            $query->whereNull('order_id')
                                ->orWhere('orders.status', 'paid');
                        })
                        ->groupBy('order_items.order_id')
                        ->orderBy('remain_count', 'desc');
                    break;
                case 'created_at_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'created_at_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'expire_at_asc':
                    $query->orderBy('expired_at', 'asc');
                    break;
                case 'expire_at_desc':
                    $query->orderBy('expired_at', 'desc');
                    break;
            }
        }

        return $query;
    }

    public function create()
    {
        $this->authorize('admin_discount_codes_create');

        $data = [
            'pageTitle' => trans('admin/main.new_discount_title'),
        ];

        return view('admin.financial.discount.new', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_discount_codes_create');

        $this->validate($request, [
            'title' => 'required',
            'code' => 'required|unique:discounts',
            'user_id' => 'nullable',
            'percent' => 'nullable',
            'amount' => 'nullable',
            'count' => 'nullable',
            'expired_at' => 'required',
        ]);

        $data = $request->all();
        $user_id = $data['user_id'] ?? [];

        $discountType = 'all_users';
        if (!empty($user_id)) {
            $discountType = 'special_users';
        }

        $discount = Discount::create([
            'creator_id' => auth()->id(),
            'title' => $data['title'],
            'code' => $data['code'],
            'percent' => (!empty($data['percent']) and $data['percent'] > 0) ? $data['percent'] : 0,
            'amount' => $data['amount'],
            'count' => (!empty($data['count']) and $data['count'] > 0) ? $data['count'] : 1,
            'type' => $discountType,
            'status' => 'active',
            'expired_at' => strtotime($data['expired_at']),
            'created_at' => time(),
        ]);

        if (!empty($user_id)) {
            DiscountUser::create([
                'discount_id' => $discount->id,
                'user_id' => $user_id,
                'created_at' => time(),
            ]);
        }

        return redirect('/admin/financial/discounts');
    }

    public function edit($id)
    {
        $this->authorize('admin_discount_codes_edit');

        $discount = Discount::findOrFail($id);
        $userDiscounts = DiscountUser::where('discount_id', $id)->get();

        $data = [
            'pageTitle' => trans('admin/main.edit_discount_title'),
            'discount' => $discount,
            'userDiscounts' => $userDiscounts,
        ];

        return view('admin.financial.discount.new', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_discount_codes_edit');

        $discount = Discount::findOrFail($id);

        $this->validate($request, [
            'title' => 'required',
            'code' => 'required|unique:discounts,code,' . $discount->id,
            'user_id' => 'nullable',
            'percent' => 'nullable',
            'amount' => 'nullable',
            'count' => 'nullable',
            'expired_at' => 'required',
        ]);

        $data = $request->all();
        $user_id = $data['user_id'] ?? [];

        $discountType = 'all_users';
        if (!empty($user_id)) {
            $discountType = 'special_users';
        }

        $discount->update([
            'title' => $data['title'],
            'code' => $data['code'],
            'percent' => (!empty($data['percent']) and $data['percent'] > 0) ? $data['percent'] : 0,
            'amount' => $data['amount'],
            'count' => (!empty($data['count']) and $data['count'] > 0) ? $data['count'] : 1,
            'type' => $discountType,
            'status' => 'active',
            'expired_at' => strtotime($data['expired_at']),
        ]);

        DiscountUser::where('discount_id', $discount->id)->delete();

        if (!empty($user_id)) {
            DiscountUser::create([
                'discount_id' => $discount->id,
                'user_id' => $user_id,
                'created_at' => time(),
            ]);
        }

        return redirect('/admin/financial/discounts');
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_discount_codes_delete');

        Discount::find($id)->delete();

        return redirect('/admin/financial/discounts');
    }
}
