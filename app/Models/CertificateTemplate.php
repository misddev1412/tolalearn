<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $table = "certificates_templates";
    public $timestamps = false;
    protected $guarded = ['id'];
}
