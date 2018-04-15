<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Company extends Model
{
    //
    use Sluggable;

    public $timestamps = false;
    protected $fillable = ['name'];

    public function users(){
    	return $this->hasMany('App\User');
    }

    public function products(){
    	return $this->hasMany('App\Product');
    }

    public function collections(){
        return $this->hasMany('App\Collection');
    }
    
    public function shippings(){
        return $this->hasMany('App\Shipping');
    }

    public function orders(){
        return $this->hasMany('App\Order');
    }

    public function payment_methods(){
        return $this->hasMany('App\PaymentMethod');
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
