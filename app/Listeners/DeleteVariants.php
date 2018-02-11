<?php

namespace App\Listeners;

use App\Product;
use App\Variant;
use App\Events\ProductDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteVariants
{
    

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     *
     * @param  ProductDeleted  $event
     * @return void
     */
    public function handle(ProductDeleted $event)
    {
        //
        $product->variants()->delete();
    }
}
