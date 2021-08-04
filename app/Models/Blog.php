<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blog';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];


    use Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function category()
    {
        return $this->belongsTo('App\Models\BlogCategory', 'category_id', 'id');
    }

    public function author()
    {
        return $this->belongsTo('App\User', 'author_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'blog_id', 'id');
    }

    public function getUrl()
    {
        return '/blog/' . $this->slug;
    }
}
