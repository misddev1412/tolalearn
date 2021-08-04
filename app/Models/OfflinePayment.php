<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfflinePayment extends Model
{
    public static $waiting = 'waiting';
    public static $approved = 'approved';
    public static $reject = 'reject';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
