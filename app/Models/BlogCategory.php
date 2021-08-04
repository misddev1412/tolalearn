<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $table = 'blog_categories';
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

    public function blog()
    {
        return $this->hasMany('App\Models\Blog', 'category_id', 'id');
    }

    public function getUrl()
    {
        return '/blog/categories/' . $this->slug;
    }
}
