<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscribe extends Model
{
    protected $table = 'subscribes';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function sales()
    {
        return $this->hasMany('App\Models\Sale', 'subscribe_id', 'id');
    }

    public function uses()
    {
        return $this->hasMany('App\Models\SubscribeUse', 'subscribe_id', 'id');
    }

    public static function getActiveSubscribe($userId)
    {
        $activePlan = false;
        $lastSubscribeSale = Sale::where('buyer_id', $userId)
            ->where('type', Sale::$subscribe)
            ->latest('created_at')
            ->first();

        if ($lastSubscribeSale) {
            $subscribe = $lastSubscribeSale->subscribe;

            $useCount = SubscribeUse::where('user_id', $userId)
                ->where('subscribe_id', $subscribe->id)
                ->count();

            $subscribe->used_count = $useCount;

            $countDayOfSale = (int)diffTimestampDay(time(), $lastSubscribeSale->created_at);
            if (
                $subscribe->usable_count > $useCount
                and
                $subscribe->days >= $countDayOfSale
            ) {
                $activePlan = $subscribe;
            }
        }

        return $activePlan;
    }

    public static function getDayOfUse($userId)
    {
        $lastSubscribeSale = Sale::where('buyer_id', $userId)
            ->where('type', Sale::$subscribe)
            ->whereNull('refund_at')
            ->latest('created_at')
            ->first();

        return $lastSubscribeSale ? (int)diffTimestampDay(time(), $lastSubscribeSale->created_at) : 0;
    }
}
