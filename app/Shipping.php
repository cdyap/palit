<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Shipping extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $fillable = ['name', 'description', 'is_active', 'price'];
    protected $dates = ['deleted_at'];

    public function company(){
    	return $this->belongsTo('App\Company');
    }

    public function getViewPriceAttribute(){
        return $this->currency . " " . number_format($this->price, 2, '.', ',');
    }
}
