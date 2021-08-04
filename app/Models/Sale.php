<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public static $webinar = 'webinar';
    public static $meeting = 'meeting';
    public static $subscribe = 'subscribe';
    public static $promotion = 'promotion';

    public static $credit = 'credit';
    public static $paymentChannel = 'payment_channel';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }

    public function buyer()
    {
        return $this->belongsTo('App\User', 'buyer_id', 'id');
    }

    public function seller()
    {
        return $this->belongsTo('App\User', 'seller_id', 'id');
    }

    public function meeting()
    {
        return $this->belongsTo('App\Models\Meeting', 'meeting_id', 'id');
    }

    public function subscribe()
    {
        return $this->belongsTo('App\Models\Subscribe', 'subscribe_id', 'id');
    }

    public function promotion()
    {
        return $this->belongsTo('App\Models\Promotion', 'promotion_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }

    public function ticket()
    {
        return $this->belongsTo('App\Models\Ticket', 'ticket_id', 'id');
    }

    public function saleLog()
    {
        return $this->hasOne('App\Models\SaleLog', 'sale_id', 'id');
    }

    public static function createSales($orderItem, $payment_method)
    {
        $orderType = Order::$webinar;
        if (!empty($orderItem->reserve_meeting_id)) {
            $orderType = Order::$meeting;
        } elseif (!empty($orderItem->subscribe_id)) {
            $orderType = Order::$subscribe;
        } elseif (!empty($orderItem->promotion_id)) {
            $orderType = Order::$promotion;
        }

        $seller_id = OrderItem::getSeller($orderItem);

        $sale = Sale::create([
            'buyer_id' => $orderItem->user_id,
            'seller_id' => $seller_id,
            'order_id' => $orderItem->order_id,
            'webinar_id' => $orderItem->webinar_id,
            'meeting_id' => !empty($orderItem->reserve_meeting_id) ? $orderItem->reserveMeeting->meeting_id : null,
            'subscribe_id' => $orderItem->subscribe_id,
            'promotion_id' => $orderItem->promotion_id,
            'type' => $orderType,
            'payment_method' => $payment_method,
            'amount' => $orderItem->amount,
            'tax' => $orderItem->tax_price,
            'commission' => $orderItem->commission_price,
            'discount' => $orderItem->discount,
            'total_amount' => $orderItem->total_amount,
            'created_at' => time(),
        ]);

        $title = '';
        if (!empty($orderItem->webinar_id)) {
            $title = $orderItem->webinar->title;
        } else if (!empty($orderItem->meeting_id)) {
            $title = trans('meeting.reservation_appointment');
        } else if (!empty($orderItem->subscribe_id)) {
            $title = $orderItem->subscribe->title . ' ' . trans('financial.subscribe');
        } else if (!empty($orderItem->promotion_id)) {
            $title = $orderItem->promotion->title . ' ' . trans('panel.promotion');
        }

        $notifyOptions = [
            '[c.title]' => $title,
        ];

        sendNotification('new_sales', $notifyOptions, $seller_id);
        sendNotification('new_purchase', $notifyOptions, $orderItem->user_id);

        if ($orderItem->reserve_meeting_id) {
            $reserveMeeting = $orderItem->reserveMeeting;

            $notifyOptions = [
                '[amount]' => $orderItem->amount,
                '[u.name]' => $orderItem->user->full_name,
                '[time.date]' => $reserveMeeting->day . ' ' . $reserveMeeting->time,
            ];
            sendNotification('new_appointment', $notifyOptions, $orderItem->user_id);
            sendNotification('new_appointment', $notifyOptions, $reserveMeeting->meeting->creator_id);
        }

        return $sale;
    }

    public function getIncomeItem()
    {
        if ($this->payment_method == self::$subscribe) {
            $used = SubscribeUse::where('webinar_id', $this->webinar_id)
                ->where('sale_id', $this->id)
                ->first();

            if (!empty($used)) {
                $subscribe = $used->subscribe;

                $financialSettings = getFinancialSettings();
                $commission = $financialSettings['commission'];

                $pricePerSubscribe = $subscribe->price / $subscribe->usable_count;
                $commissionPrice = $pricePerSubscribe * $commission / 100;

                return round($pricePerSubscribe - $commissionPrice, 2);
            }
        }

        $income = $this->total_amount - $this->tax - $this->commission;
        return round($income, 2);
    }

    public function getUsedSubscribe($user_id, $webinar_id)
    {
        $subscribe = null;
        $use = SubscribeUse::where('sale_id', $this->id)
            ->where('webinar_id', $webinar_id)
            ->where('user_id', $user_id)
            ->first();

        if (!empty($use)) {
            $subscribe = Subscribe::where('id', $use->subscribe_id)->first();
        }

        return $subscribe;
    }
}
