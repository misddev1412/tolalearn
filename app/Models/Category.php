<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    static $cacheKey = 'categories';

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'parent_id', 'id');
    }

    public function subCategories()
    {
        return $this->hasMany($this, 'parent_id', 'id')->orderBy('order', 'asc');
    }

    public function filters()
    {
        return $this->hasMany('App\Models\Filter', 'category_id', 'id');
    }

    public function webinars()
    {
        return $this->hasMany('App\Models\Webinar', 'category_id', 'id');
    }

    public function userOccupations()
    {
        return $this->hasMany('App\Models\UserOccupation', 'category_id', 'id');
    }

    public function getUrl()
    {
        return '/categories/' . str_replace(' ', '-', $this->title);
    }

    static function getCategories()
    {
        $categories = cache()->remember(self::$cacheKey, 24 * 60 * 60, function () {
            return self::whereNull('parent_id')
                ->with([
                    'subCategories' => function ($query) {
                        $query->orderBy('order', 'asc');
                    },
                ])
                ->get();
        });

        return $categories;
    }

    public function getCategoryCourses()
    {
        $webinars = collect([]);
        $subCategories = $this->subCategories;

        foreach ($subCategories as $category) {
            $webinars = $webinars->merge($category->webinars);
        }

        return $webinars;
    }

    public function getCategoryInstructorsIdsHasMeeting()
    {
        $ids = [];
        $subCategories = $this->subCategories;

        foreach ($subCategories as $category) {
            if (count($category->userOccupations)) {
                foreach ($category->userOccupations as $occupation) {
                    if (!empty($occupation->user) and !$occupation->user->isUser() and !$occupation->user->isAdmin()) {
                        if (!empty($occupation->user->hasMeeting())) {
                            $ids[] = $occupation->user->id;
                        }
                    }
                }
            }
        }

        return $ids;
    }
}
