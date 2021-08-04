<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = "certificates";
    public $timestamps = false;
    protected $guarded = ['id'];

    public function quiz()
    {
        return $this->hasOne('App\Models\Quiz', 'id', 'quiz_id');
    }

    public function student()
    {
        return $this->hasOne('App\User', 'id', 'student_id');
    }

    public function quizzesResult()
    {
        return $this->hasOne('App\Models\QuizzesResult', 'id', 'quiz_result_id');
    }
}
