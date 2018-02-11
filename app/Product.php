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
    protected $fillable = ['name', 'description', 'image_url', 'slug', 'is_shipped', 'currency', 'price', 'is_available', 'SKU', 'quantity'];
    
    use Sluggable;
    use SoftDeletes;
    
    public function collections()
    {
        return $this->belongsToMany('App\Collection');
    }

    public function variants(){
    	return $this->hasMany('App\Variant');
    }

    public function company(){
        return $this->belongsTo('App\Company');
    }

    public function view_price(){
        return $this->currency . " " . number_format($this->price, 2, '.', ',');
    }

    public function getTotalInventoryAttribute(){
        if ($this->variants->count() > 0) {
            return $this->variants->sum('inventory');
        } else {
            return $this->quantity;
        }
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
    	return Setting::where('name', 'variant_' . $this->id)->where('company_id', Auth::user()->company->id)->orderBy('value_2')->get();
    }

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public static function boot()
    {
        parent::boot();
        
        // Attach event handler, on deleting of the product
        Product::deleting(function($product)
        {   
            $product->slug = null;
            $product->save();

            foreach ($product->variants as $variant) {
                $variant->delete();
            }
        });
    }
}
