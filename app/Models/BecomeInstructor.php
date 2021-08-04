<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BecomeInstructor extends Model
{
    protected $table = 'become_instructors';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
