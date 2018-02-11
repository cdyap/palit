<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Company extends Model
{
    //
    use Sluggable;

    public $timestamps = false;

    public function users(){
    	return $this->hasMany('App\User');
    }

    public function products(){
    	return $this->hasMany('App\Products');
    }
    
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function settings(){
    	return $this->hasMany('App\Setting');
    }

    public function getRouteKeyName()
	{
	    return 'slug';
	}
}
