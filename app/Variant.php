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

    public function product(){
    	return $this->belongsTo('App\Product');
    }

    public function view_price(){
    	return $this->product->currency . " " . number_format($this->price, 2, '.', ',');
    }

    public function getViewPriceAttribute(){
    	return $this->product->currency . " " . number_format($this->price, 2, '.', ',');
    }
}
