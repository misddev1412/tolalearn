<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\CalendarLinks\Link;

class Session extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $table = 'sessions';
    protected $dateFormat = 'U';

    public function creator()
    {
        return $this->hasOne('App\User', 'user_id', 'id');
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }

    public function sessionReminds()
    {
        return $this->hasMany('App\Models\SessionRemind', 'session_id', 'id');
    }

    public function learningStatus()
    {
        return $this->hasOne('App\Models\CourseLearning', 'session_id', 'id');
    }

    public function addToCalendarLink()
    {
        $date = \DateTime::createFromFormat('Y-m-d H:i', dateTimeFormat($this->date, 'Y-m-d H:i'));

        $link = Link::create($this->title, $date, $date); //->description('Cookies & cocktails!')

        return $link->google();
    }

    public function getJoinLink($zoom_start_link = false)
    {
        $link = $this->link;

        if ($this->session_api == 'big_blue_button') {
            $link = env('APP_URL') . 'panel/sessions/' . $this->id . '/joinToBigBlueButton';
        }

        if ($zoom_start_link and auth()->check() and auth()->id() == $this->creator_id and $this->session_api == 'zoom') {
            $link = $this->zoom_start_link;
        }

        return $link;
    }
}
