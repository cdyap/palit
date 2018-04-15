<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryLog extends Model
{
    //
    public function delivery(){
    	return $this->belongsTo('App\Delivery');
    }
}
