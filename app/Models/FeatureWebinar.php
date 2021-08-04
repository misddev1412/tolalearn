<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureWebinar extends Model
{
    protected $dateFormat = 'U';
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $table = 'feature_webinars';

    static $pages = ['categories', 'home', 'home_categories'];

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }
}
