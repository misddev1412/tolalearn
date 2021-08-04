<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $table = 'testimonials';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
