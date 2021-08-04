<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilterOption extends Model
{
    protected $table = 'filter_options';
    public $timestamps = false;

    protected $guarded = ['id'];
}
