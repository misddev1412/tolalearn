<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextLesson extends Model
{
    protected $table = 'text_lessons';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function attachments()
    {
        return $this->hasMany('App\Models\TextLessonAttachment', 'text_lesson_id', 'id');
    }

    public function learningStatus()
    {
        return $this->hasOne('App\Models\CourseLearning', 'text_lesson_id', 'id');
    }
}
