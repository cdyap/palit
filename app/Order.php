<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    //
    public $timestamps = false;
    protected $appends = ['total'];

    public function company(){
    	return $this->belongsTo('App\Company');
    }

    public function order_items(){
    	return $this->hasMany('App\OrderItem');
    }

    public function view_price(){
    	return $this->company->currency . " "  . number_format($this->order_items()->sum('price'),2,'.',',');
    }

    public function getDateOrderedAttribute($value){
    	return Carbon::parse($value)->toFormattedDateString();
    }

    public function getDatePaidAttribute($value){
        return Carbon::parse($value)->toFormattedDateString();
    }

    public function view_shipping_price(){
        return $this->company->currency . " "  . number_format($this->shipping_price,2,'.',',');
    }

    public function total(){
        return $this->company->currency . " "  . number_format($this->order_items->sum('price') + $this->shipping_price,2,'.',',');
    }
}
