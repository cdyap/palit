<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Collection extends Model
{
    //
    public $timestamps = false;
    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'description'];
    use Sluggable;

    public function products()
    {
        return $this->belongsToMany('App\Product');
    }

    public function company(){
    	return $this->belongsTo('App\Company');
    }

    public function total_inventory(){
    	if ($this->products->sum('total_inventory') > 0)
	    	return $this->products->sum('total_inventory');
	    else
	    	return 0;
    }
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public static function boot()
    {
        parent::boot();
        
        // Attach event handler, on deleting of the collection
        Collection::deleting(function($collection)
        {   
            $collection->slug = null;
            $collection->products()->detach();
            $collection->save();
        });
    }
}
