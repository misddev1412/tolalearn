<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];
    static $discountTypes = ['all_users', 'special_users'];

    public function discountUsers()
    {
        return $this->hasOne('App\Models\DiscountUser', 'discount_id', 'id');
    }

    public function discountRemain()
    {
        $count = $this->count;

        $orderItems = OrderItem::where('discount_id', $this->id)
            ->groupBy('order_id')
            ->get();

        foreach ($orderItems as $orderItem) {
            if ($orderItem->order->status == 'paid') {
                $count = $count - 1;
            }
        }

        return ($count > 0) ? $count : 0;
    }

    public function checkValidDiscount()
    {
        $user = auth()->user();
        $valid = true;

        if ($this->expired_at < time()) {
            $valid = false;
        }

        if ($this->type == 'special_users') {
            $userDiscount = DiscountUser::where('user_id', $user->id)
                ->where('discount_id', $this->id)
                ->first();

            if (empty($userDiscount)) {
                $valid = false;
            }
        }

        if ($valid) {
            $usedCount = 0;
            $orderItems = OrderItem::where('discount_id', $this->id)
                ->groupBy('order_id')
                ->get();

            foreach ($orderItems as $orderItem) {
                if ($orderItem->order->status == 'paid') {
                    $usedCount += 1;
                }
            }

            if ($usedCount >= $this->count) {
                $valid = false;
            }
        }

        return $valid;
    }
}
