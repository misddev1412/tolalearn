<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscribeUse extends Model
{
    protected $table = 'subscribe_uses';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];


    public function subscribe()
    {
        return $this->belongsTo('App\Models\Subscribe', 'subscribe_id', 'id');
    }

}
