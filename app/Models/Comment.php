<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $timestamps = false;

    static $pending = 'pending';
    static $active = 'active';


    protected $guarded = ['id'];

    public function replies()
    {
        return $this->hasMany('App\Models\Comment', 'reply_id', 'id');
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function review()
    {
        return $this->belongsTo('App\Models\WebinarReview', 'review_id', 'id');
    }

    public function blog()
    {
        return $this->belongsTo('App\Models\Blog', 'blog_id', 'id');
    }
}
