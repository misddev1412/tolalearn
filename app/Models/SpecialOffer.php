<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialOffer extends Model
{
    protected $table = 'special_offers';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public static $active = 'active';
    public static $inactive = 'inactive';

    public function webinar()
    {
        return $this->hasOne('App\Models\Webinar', 'id', 'webinar_id');
    }

    public function getRemainingTimes()
    {
        $current_time = time();
        $date = $this->to_date;
        $difference = $date - $current_time;

        return time2string($difference);
    }
}
