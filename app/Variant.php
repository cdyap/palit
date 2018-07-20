<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Product;

class Variant extends Model
{
    //
	public $timestamps = false;
	protected $appends = array('view_price', 'available_inventory', 'sellable_inventory');
	protected $dates = ['deleted_at'];
	protected $fillable = ['inventory', 'price', 'attribute_1', 'attribute_2', 'attribute_3', 'attribute_4', 'attribute_5', 'SKU'];
    
	use SoftDeletes;
    use \Rutorika\Sortable\SortableTrait;

    public function product(){
    	return $this->belongsTo('App\Product');
    }

    public function getViewPriceAttribute(){
        return number_format($this->price, 2, '.', ',');
    }

    public function delivered_variants(){
        return $this->hasMany('App\DeliveredVariant');      
    }

    public function deliveredVariantQuantity(){
        return $this->hasOne('App\DeliveredVariant')
            ->selectRaw('variant_id, sum(delivered_quantity) as aggregate')
            ->groupBy('variant_id');
    }

    public function fulfilledOrders(){
        return $this->hasOne('App\OrderItem')
            ->selectRaw('variant_id, sum(quantity) as aggregate')
            // ->where('is_fulfilled', true)
            ->groupBy('variant_id');
    }

    public function pendingOrders(){
        return $this->hasOne('App\OrderItem')
            ->selectRaw('variant_id, sum(quantity) as aggregate')
            ->where('is_fulfilled', false)
            ->groupBy('variant_id');
    }

    public function deliveredVariantInitial(){
        return $this->hasOne('App\DeliveredVariant')
            ->selectRaw('variant_id, sum(quantity) as aggregate')
            ->groupBy('variant_id');
    }

    //sum of quantity of delivered variants
    public function getIncomingInventoryAttribute(){

        // if deliveredVariantInitial is not loaded already, let's do it first
        if ( ! $this->relationLoaded('deliveredVariantInitial')) 
            $this->load('deliveredVariantInitial');
         
        $delivered_variant_quantity = $this->getRelation('deliveredVariantInitial');
        $quantity = ($delivered_variant_quantity) ? (int) $delivered_variant_quantity->aggregate : 0;

        // if deliveredVariantQuantity is not loaded already, let's do it first
        if ( ! $this->relationLoaded('deliveredVariantQuantity')) 
            $this->load('deliveredVariantQuantity');
         
        $delivered_delivered_quantity = $this->getRelation('deliveredVariantQuantity');
        $delivered = ($delivered_delivered_quantity) ? (int) $delivered_delivered_quantity->aggregate : 0;

        return $quantity - $delivered;
    }

    //initial quantity + sum of delivered quantity of delivered variants - fulfilledOrders
    public function getAvailableInventoryAttribute(){
        // if deliveredVariantQuantity is not loaded already, let's do it first
        if ( ! $this->relationLoaded('deliveredVariantQuantity')) 
            $this->load('deliveredVariantQuantity');
         
        $variant_count = $this->getRelation('deliveredVariantQuantity');
        $delivered_variant = ($variant_count) ? (int) $variant_count->aggregate : 0;

        // if fulfilledOrders is not loaded already, let's do it first
        if ( ! $this->relationLoaded('fulfilledOrders')) 
            $this->load('fulfilledOrders');
         
        $order_count = $this->getRelation('fulfilledOrders');
        $orders = ($order_count) ? (int) $order_count->aggregate : 0;

        return $this->inventory + $delivered_variant - $orders;
    }
   
    public function getSellableInventoryAttribute(){
        // if deliveredVariantQuantity is not loaded already, let's do it first
        if ( ! $this->relationLoaded('deliveredVariantQuantity')) 
            $this->load('deliveredVariantQuantity');
         
        $variant_count = $this->getRelation('deliveredVariantQuantity');
        $delivered_variant = ($variant_count) ? (int) $variant_count->aggregate : 0;

        // if fulfilledOrders is not loaded already, let's do it first
        if ( ! $this->relationLoaded('fulfilledOrders')) 
            $this->load('fulfilledOrders');
         
        $order_count = $this->getRelation('fulfilledOrders');
        $orders = ($order_count) ? (int) $order_count->aggregate : 0;

        // if pendingOrders is not loaded already, let's do it first
        if ( ! $this->relationLoaded('pendingOrders')) 
            $this->load('pendingOrders');

        $pending_orders = $this->getRelation('pendingOrders');
        $pending_orders = ($pending_orders) ? (int) $pending_orders->aggregate : 0;

        return $this->inventory + $delivered_variant - $orders - $pending_orders;
    }

    public function getDescriptionAttribute(){
        $description = $this->attribute_1;

        if (!empty($this->attribute_2)) {
            $description = $description . ", " . $this->attribute_2;
        }

        if (!empty($this->attribute_3)) {
            $description = $description . ", " . $this->attribute_3;
        }

        if (!empty($this->attribute_4)) {
            $description = $description . ", " . $this->attribute_4;
        }

        if (!empty($this->attribute_5)) {
            $description = $description . ", " . $this->attribute_5;
        }

        return $description;
    }
}
