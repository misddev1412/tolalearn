<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    protected $table = 'faqs';
    public $timestamps = false;

    protected $guarded = ['id'];
}
