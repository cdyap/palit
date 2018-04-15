<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    //
    public $timestamps = false;
    protected $fillable = ['name', 'description', 'is_available'];

    public function company(){
    	return $this->belongsto('App\Company');
    }
}
