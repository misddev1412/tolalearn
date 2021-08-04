<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function options()
    {
        return $this->hasMany('App\Models\FilterOption', 'filter_id', 'id')->orderBy('order', 'asc');
    }
}
