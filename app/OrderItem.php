<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    //
    public $timestamps = false;
    protected $appends = ['is_variant', 'total_price'];

    public function order(){
    	return $this->belongsTo('App\Order');
    }

    public function product(){
    	return $this->hasOne('App\Product');
    }

    public function variant(){
		return $this->hasOne('App\Variant');
    }

    public function getIsVariantAttribute(){
    	if(empty($this->variant_id)) {
    		return false;
    	} else {
    		return true;
    	}
    }

    public function getTotalPriceAttribute(){
        return $this->quantity * $this->price;
    }
}
