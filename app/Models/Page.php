<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

}
