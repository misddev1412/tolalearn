<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Cart extends Model
{
    protected $table = "cart";

    public $timestamps = false;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'creator_id', 'id');
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }

    public function reserveMeeting()
    {
        return $this->belongsTo('App\Models\ReserveMeeting', 'reserve_meeting_id', 'id');
    }

    public function ticket()
    {
        return $this->belongsTo('App\Models\Ticket', 'ticket_id', 'id');
    }

    public static function emptyCart($userId)
    {
        Cart::where('creator_id', $userId)->delete();
    }
}
