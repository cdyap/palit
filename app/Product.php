<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Setting;
use Auth;

class Product extends Model
{
    //
    public $timestamps = false;
    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'description', 'image_url', 'slug', 'is_shipped', 'currency', 'price', 'is_available', 'SKU', 'quantity', 'item_per_shipment', 'overselling_allowed'];
    protected $appends = ['shipping_factor'];

    use Sluggable;
    use SoftDeletes;

    public function collections()
    {
        return $this->belongsToMany('App\Collection');
    }

    public function variants(){
    	return $this->hasMany('App\Variant')->orderBy('position');
    }

    public function company(){
        return $this->belongsTo('App\Company');
    }

    public function delivered_products(){
        return $this->hasMany('App\DeliveredProduct');      
    }

    public function delivered_variants()
    {
        return $this->hasMany('App\DeliveredVariant');
    }

    public function view_price(){
        return $this->currency . " " . number_format($this->price, 2, '.', ',');
    }

    public function getTotalInventoryAttribute(){
        if ($this->variants->count() > 0) {
            return $this->variants->sum('inventory');
        } else {
            if(!empty($this->quantity))
                return $this->quantity;
            else 
                return 0;
        }
    }

    //get counts
    public function variantQuantity(){
        return $this->hasOne('App\Variant')
            ->selectRaw('product_id, sum(inventory) as aggregate')
            ->groupBy('product_id');
    }

    public function deliveredVariantQuantity(){
        return $this->hasOne('App\DeliveredVariant')
            ->selectRaw('product_id, sum(delivered_quantity) as aggregate')
            ->groupBy('product_id');
    }

    public function deliveredProductQuantity(){
        return $this->hasOne('App\DeliveredProduct')
            ->selectRaw('product_id, sum(delivered_quantity) as aggregate')
            ->groupBy('product_id');
    }

    public function fulfilledOrders(){
        return $this->hasOne('App\OrderItem')
            ->selectRaw('product_id, sum(quantity) as aggregate')
            ->where('is_fulfilled', true)
            ->groupBy('product_id');
    }

    public function getAvailableInventoryAttribute(){
        // if variantQuantity is not loaded already, let's do it first
        if ( ! $this->relationLoaded('variantQuantity')) 
            $this->load('variantQuantity');
         
        $variant_count = $this->getRelation('variantQuantity');
        $variant = ($variant_count) ? (int) $variant_count->aggregate : 0;

        // if deliveredVariantQuantity is not loaded already, let's do it first
        if ( ! $this->relationLoaded('deliveredVariantQuantity')) 
            $this->load('deliveredVariantQuantity');
         
        $delivered_variant_count = $this->getRelation('deliveredVariantQuantity');
        $delivered_variant = ($delivered_variant_count) ? (int) $delivered_variant_count->aggregate : 0;

        // if deliveredProductQuantity is not loaded already, let's do it first
        if ( ! $this->relationLoaded('deliveredProductQuantity')) 
            $this->load('deliveredProductQuantity');
         
        $delivered_product_count = $this->getRelation('deliveredProductQuantity');
        $delivered_product = ($delivered_product_count) ? (int) $delivered_product_count->aggregate : 0;

        // if fulfilledOrders is not loaded already, let's do it first
        if ( ! $this->relationLoaded('fulfilledOrders')) 
            $this->load('fulfilledOrders');
         
        $order_count = $this->getRelation('fulfilledOrders');
        $orders = ($order_count) ? (int) $order_count->aggregate : 0;

        return $variant  + $delivered_variant + $delivered_product + $this->quantity - $orders;
    }

    public function deliveredVariantInitial(){
        return $this->hasOne('App\DeliveredVariant')
            ->selectRaw('product_id, sum(quantity) as aggregate')
            ->groupBy('product_id');
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

    //if item_per_shipment is 2, 2 pieces will fit in one shipment. Each item will occupy 1/2 space.
    public function getShippingFactorAttribute(){
        return 1 / $this->item_per_shipment;
    }

    public function hasSameVariantPrices(){
        if ($this->variants->count() > 1) {
            if (count(array_unique($this->variants->pluck('view_price')->all())) === 1)
                return true;
            else
                return false;
        } else {
            return true;
        }
        
    }

    public function variant_columns(){
    	return Setting::where('name', 'variant_' . $this->id)->orderBy('value_2')->get();
    }

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
