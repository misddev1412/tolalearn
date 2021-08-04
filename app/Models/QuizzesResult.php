<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizzesResult extends Model
{
    static $passed = 'passed';
    static $failed = 'failed';
    static $waiting = 'waiting';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function quiz()
    {
        return $this->belongsTo('App\Models\Quiz', 'quiz_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

}
