<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveredVariant extends Model
{
    //
    protected $appends = ['incoming_inventory'];
    
    public function delivery(){
    	return $this->belongsTo('App\Delivery');
    }

    public function variant(){
    	return $this->belongsTo('App\Variant');
    }

    public function product(){
    	return $this->belongsTo('App\Product');
    }

    public function getIncomingInventoryAttribute(){
        return $this->quantity - $this->delivered_quantity;
    }
}
