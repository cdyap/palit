<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    public $timestamps = false;

    public function company(){
    	return $this->belongsTo('App\Company');
    }
}
