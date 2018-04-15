<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Product;

class Variant extends Model
{
    //
	public $timestamps = false;
	protected $appends = array('view_price');
	protected $dates = ['deleted_at'];
	protected $fillable = ['inventory', 'price', 'attribute_1', 'attribute_2', 'attribute_3', 'attribute_4', 'attribute_5', 'SKU'];
    
	use SoftDeletes;
    use \Rutorika\Sortable\SortableTrait;

    public function product(){
    	return $this->belongsTo('App\Product');
    }

    public function view_price(){
    	return $this->product->currency . " " . number_format($this->price, 2, '.', ',');
    }

    public function getViewPriceAttribute(){
    	return $this->product->currency . " " . number_format($this->price, 2, '.', ',');
    }

    public function getIncomingInventoryAttribute(){
        return $this->delivered_variants->sum('incoming_inventory');
    }

    public function getAvailableInventoryAttribute(){
        return $this->delivered_variants->sum('delivered_quantity') + $this->inventory;
    }

    public function delivered_variants(){
        return $this->hasMany('App\DeliveredVariant');      
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
