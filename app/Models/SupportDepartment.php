<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportDepartment extends Model
{
    protected $table = 'support_departments';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function supports()
    {
        return $this->hasMany('App\Models\Support', 'department_id', 'id');
    }
}
