<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Carbon\Carbon;
use Auth;

class Delivery extends Model
{
    use Sluggable;
    //
    public function delivered_variants(){
		return $this->hasMany('App\DeliveredVariant');    	
    }

    public function delivered_products(){
		return $this->hasMany('App\DeliveredProduct');    	
    }

    public function delivery_logs(){
        return $this->hasMany('App\DeliveryLog');
    }

    public function deliveredVariantsCount(){
        return $this->hasOne('App\DeliveredVariant')
            ->selectRaw('delivery_id, count(*) as aggregate')
            ->groupBy('delivery_id');
    }

    public function deliveredProductsCount(){
        return $this->hasOne('App\DeliveredProduct')
            ->selectRaw('delivery_id, count(*) as aggregate')
            ->groupBy('delivery_id');
    }

    public function getItemsInCartAttribute(){
        // if deliveredVariantsCount is not loaded already, let's do it first
        if ( ! $this->relationLoaded('deliveredVariantsCount')) 
            $this->load('deliveredVariantsCount');
         
        $delivered_variants_count = $this->getRelation('deliveredVariantsCount');
        $delivered_variants = ($delivered_variants_count) ? (int) $delivered_variants_count->aggregate : 0;

        // if deliveredProductsCount is not loaded already, let's do it first
        if ( ! $this->relationLoaded('deliveredProductsCount')) 
            $this->load('deliveredProductsCount');
         
        $delivered_products_count = $this->getRelation('deliveredProductsCount');
        $delivered_products = ($delivered_products_count) ? (int) $delivered_products_count->aggregate : 0;


        return $delivered_variants + $delivered_products;
    }

    public function getCreatedAtAttribute($value){
    	return Carbon::parse($value)->setTimezone(Auth::user()->timezone)->format('M d, Y');
    }

    public function getExpectedArrivalAttribute($value){
    	return Carbon::parse($value)->setTimezone(Auth::user()->timezone)->format('M d, Y');
    }

    public function getRemainingDaysAttribute(){
        return Carbon::now()->diffInDays(Carbon::parse($this->expected_arrival));
    }

    public function getTotalInventoryAttribute(){
    	$variants_quantity = 0;
    	$products_quantity = 0;

    	if ($this->delivered_variants->count() > 0)
    		$variants_quantity = $this->delivered_variants->sum('quantity');

    	if ($this->delivered_products->count() > 0)
    		$variants_quantity = $this->delivered_products->sum('quantity');

    	return $variants_quantity + $products_quantity;
    }

    public function sluggable(){
        return [
            'slug' => [
                'source' => 'supplier', 'expected_arrival',
                'separator' => '-'
            ]
        ];
    }
}
