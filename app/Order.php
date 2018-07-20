<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    //
    use SoftDeletes;

    public $timestamps = false;
    protected $appends = ['total', 'total_trashed'];
    protected $dates = ['deleted_at'];

    public function company(){
    	return $this->belongsTo('App\Company');
    }

    public function order_items(){
    	return $this->hasMany('App\OrderItem');
    }

    public function order_items_trashed(){
        return $this->hasMany('App\OrderItem')->onlyTrashed();
    }

    public function ordersQuantity(){
        return $this->hasOne('App\OrderItem')
            ->selectRaw('order_id, sum(quantity * price) as aggregate')
            ->groupBy('order_id');
    }

    //sum of quantity of delivered variants
    public function getTotalAttribute(){
        // if ordersQuantity is not loaded already, let's do it first
        if ( ! $this->relationLoaded('ordersQuantity')) 
            $this->load('ordersQuantity');
         
        $orders_quantity = $this->getRelation('ordersQuantity');
        $quantity = ($orders_quantity) ? (int) $orders_quantity->aggregate : 0;

        if ( ! $this->relationLoaded('company')) 
            $this->load('company');

        return $this->company->currency . " "  . number_format($quantity + $this->shipping_price,2,'.',',');
    }

    public function ordersQuantityTrashed(){
        return $this->hasOne('App\OrderItem')
            ->selectRaw('order_id, sum(quantity * price) as aggregate')
            ->onlyTrashed()
            ->groupBy('order_id');
    }

    //sum of quantity of delivered variants
    public function getTotalTrashedAttribute(){
        // if ordersQuantity is not loaded already, let's do it first
        if ( ! $this->relationLoaded('ordersQuantityTrashed')) 
            $this->load('ordersQuantityTrashed');
         
        $orders_quantity = $this->getRelation('ordersQuantityTrashed');
        $quantity = ($orders_quantity) ? (int) $orders_quantity->aggregate : 0;

        if ( ! $this->relationLoaded('company')) 
            $this->load('company');

        return $this->company->currency . " "  . number_format($quantity + $this->shipping_price,2,'.',',');
    }

    public function view_price(){
    	return $this->company->currency . " "  . number_format($this->order_items()->sum('price'),2,'.',',');
    }

    public function getDateOrderedAttribute($value){
        // if (empty($value)) {
        //     return $value;
        // } else {
        //     return Carbon::parse($value)->toFormattedDateString();    
        // }	

        return (empty($value)) ? $value : Carbon::parse($value)->toFormattedDateString();
    }

    public function getDatePaidAttribute($value){
        return (empty($value)) ? $value : Carbon::parse($value)->toFormattedDateString();
    }

    public function getDateFulfilledAttribute($value){
        return (empty($value)) ? $value : Carbon::parse($value)->toFormattedDateString();
    }

    public function getDeletedAtAttribute($value){
        return (empty($value)) ? $value : Carbon::parse($value)->toFormattedDateString();
    }

    public function view_shipping_price(){
        return $this->company->currency . " "  . number_format($this->shipping_price,2,'.',',');
    }

    // public function total(){
        // return $this->company->currency . " "  . number_format($this->order_items->sum('price') + $this->shipping_price,2,'.',',');
    // }
}
