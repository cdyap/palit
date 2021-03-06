<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveredProduct extends Model
{
    //
    protected $appends = ['incoming_inventory'];

    public function delivery(){
    	return $this->belongsTo('App\Delivery');
    }

    public function product(){
    	return $this->belongsTo('App\Product');
    }

    public function getIncomingInventoryAttribute(){
        return $this->quantity - $this->delivered_quantity;
    }
}
