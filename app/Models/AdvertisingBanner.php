<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisingBanner extends Model
{
    protected $table = 'advertising_banners';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    static $positions = [
        'home1', 'home2', 'course', 'course_sidebar'
    ];

    static $size = [
        '12' => 'full',
        '6' => '1/2',
        '4' => '1/3',
        '3' => '1/4'
    ];
}
