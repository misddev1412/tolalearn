<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    public $timestamps = false;
    protected $table = 'files';
    protected $guarded = ['id'];

    static $accessibility = [
        'free', 'paid'
    ];

    static $videoTypes = ['mp4', 'mkv', 'avi'];
    static $fileTypes = [
        'pdf', 'power point', 'sound', 'video', 'image', 'archive', 'document', 'project'
    ];

    public function learningStatus()
    {
        return $this->hasOne('App\Models\CourseLearning', 'file_id', 'id');
    }

    public function isVideo()
    {
        return (in_array($this->file_type, self::$videoTypes));
    }
}
