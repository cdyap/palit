<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Product;
use App\Variant;
use App\Setting;
use App\Shipping;
use App\Order;
use App\AppSettings;
use Carbon\Carbon;
use Auth;
use Cart;
use Log;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //admin-side orders
    public function index()
    {
        //
        $sidebar = "Orders";
        $company = Auth::user()->company;
        return view('admin.orders',compact('sidebar', 'company'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }



    //order page orders
    public function order_page($slug){
        $company = Company::where('slug', $slug)->with('products', 'collections', 'products.variants', 'products.delivered_products', 'products.delivered_variants')->firstOrFail();

        //get cart for this company
        $cart = Cart::instance($company->id)->content();
        $cart_itemcount = Cart::count();
        $cart_total = Cart::subtotal();
        $product_variant_columns = Setting::where('company_id', $company->id)->whereIn('name', preg_filter('/^/', 'variant_',$cart->pluck('options')->pluck('product')->pluck('id')->toArray()))->orderBy('value_2')->get();

        return view('order_page.index',compact('company', 'cart', 'product_variant_columns', 'cart_itemcount', 'cart_total'));
    }

    public function cart($slug){
        $company = Company::where('slug', $slug)->with('products', 'collections', 'products.variants', 'products.delivered_products', 'products.delivered_variants')->firstOrFail();

        //get cart for this company
        $cart = Cart::instance($company->id)->content();

        $product_variant_columns = Setting::where('company_id', $company->id)->whereIn('name', preg_filter('/^/', 'variant_',$cart->pluck('options')->pluck('product')->pluck('id')->toArray()))->orderBy('value_2')->get();

        return view('order_page.cart',compact('company', 'cart', 'product_variant_columns'));
    }

    public function addToCart($slug, Request $request){
        $company = Company::where('slug', $slug)->firstOrFail();
        //get product from parameter containing product ID. Parameters always only have 1 ID so get the first ID.
        $product = Product::with('variants')->findOrFail(collect($request->except('_token'))->keys()[0]);
        $variant_ids = array_keys(collect($request->except('_token'))->first());
        $variants = $product->variants->whereIn('id', $variant_ids);

        //use cart for this company
        foreach($variants as $variant) {
            Cart::instance($company->id)->add($variant->product, "Product", 1, $variant->price, ['variant' => $variant, 'currency' => $variant->product->currency, 'product' => $product]);
        }

        return redirect('/'.$slug)->with(['success' => "Product added to cart. Please enter quantity to order."]);
    }

    public function removeFromCart($slug, $rowId){
        try {
            $company = Company::where('slug', $slug)->firstOrFail();
            $cart = Cart::instance($company->id)->content();
            $item = Cart::get($rowId);
            Cart::remove($rowId);

            return response()->json([
                'name' => $item->name,
                'rowId' => $rowId,
                'success' => true,
                'cart_itemcount' => Cart::count(),
                'cart_total' => Cart::subtotal()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'name' => $e,
                'rowId' => $rowId,
                'success' => false
            ]);
        }
    }

    public function changeQuantity($slug, $rowId, Request $request){
        try {
            //parse JSON input
            $input = json_decode($request->getContent(), true);

            $company = Company::where('slug', $slug)->firstOrFail();
            $cart = Cart::instance($company->id)->content();
            $item = Cart::update($rowId, $input['quantity']);

            return response()->json([
                'rowId' => $rowId,
                'success' => true,
                'item_price' => $item->options->currency . " " . number_format($item->price*$item->qty, 2, '.', ','),
                'cart_itemcount' => Cart::count(),
                'cart_total' => Cart::subtotal()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function shipping($slug, Request $request){
        $company = Company::with('shippings')->where('slug', $slug)->firstOrFail();
        $cart = Cart::instance($company->id)->content();
        
        //if cart is empty, redirect back to order page
        if(Cart::count() == 0) {
            return redirect('/'.$company->slug);
        }
        
        //update quantities if request exists
        if($request->quantity) {
            foreach($request->quantity as $rowId => $quantity) {
                Cart::instance($company->id)->update($rowId, $quantity);
            }
        }

        $cart_itemcount = Cart::count();

        //product/variant information
        $product_variant_columns = Setting::where('company_id', $company->id)->whereIn('name', preg_filter('/^/', 'variant_',$cart->pluck('options')->pluck('product')->pluck('id')->toArray()))->orderBy('value_2')->get();

        //compute for potential shipping costs per mode
        $shippings = $company->shippings;
        foreach($shippings as $mode) {
            $shipping_qty = 0;
            $cart_cost = 0.00;
            foreach($cart->where('name', 'Product') as $item) {
                $shipping_qty = $shipping_qty + ($item->options->product->shipping_factor * $item->qty);
                $cart_cost = $cart_cost + ($item->qty * $item->price);
            }
            $mode->total_cost = $mode->currency . " " . number_format(ceil($shipping_qty) * $mode->price, 2, '.', ',');
            $mode->view_cost = ceil($shipping_qty) * $mode->price;
            $mode->quantity = ceil($shipping_qty);
            $mode->cart_cost = $mode->currency . " " . number_format((ceil($shipping_qty) * $mode->price) + $cart_cost, 2, '.', ',');
        }

        //set selected_shipping_mode variable to null. Set if present in cart
        $selected_shipping_mode = null;
        if($cart->where('name', 'Shipping')->count() == 1) {
            $selected_shipping_mode = $cart->where('name', 'Shipping')->first();
        }
        
        return view('order_page.shipping',compact('company', 'cart', 'cart_itemcount', 'cart_total', 'product_variant_columns', 'shippings', 'selected_shipping_mode'));
    }

    public function checkout($slug, Request $request){
        $company = Company::with('shippings')->where('slug', $slug)->firstOrFail();
        $payment_methods = $company->payment_methods;
        
        //if cart is empty, redirect back to order page
        $cart = Cart::instance($company->id)->content();
        if(Cart::count() == 0) {
            return redirect('/'.$company->slug);
        }

        $cart = Cart::instance($company->id)->content();
        $cart_total = Cart::subtotal();

        //if has request post data and if cart doesn't have any shipping item yet
        if($request->shipping) {
            //remove any existing shipping items from cart
            if($cart->where('name', 'Shipping')->count() > 0) {
                $existing_shipping_ids = $cart->where('name', 'Shipping')->pluck('rowId')->toArray();
                foreach($existing_shipping_ids as $rowId){
                    Cart::remove($rowId);
                }
                
            }

            //compute for shipping using selected shipping mode
            $shipping_mode = Shipping::where('company_id', $company->id)->where('id', $request->shipping)->firstOrFail();
            $shipping_qty = 0;

            //compute shipping for CartItems that are products only
            foreach($cart->where('name', 'Product') as $item) {
                $shipping_qty = $shipping_qty + ($item->options->product->shipping_factor * $item->qty);
            }
            $shipping_mode->total_cost = $shipping_mode->currency . " " . number_format(ceil($shipping_qty) * $shipping_mode->price, 2, '.', ',');
            $shipping_mode->view_cost = ceil($shipping_qty) * $shipping_mode->price;
            $shipping_mode->quantity = ceil($shipping_qty);
            $shipping_mode->cart_cost = $shipping_mode->currency . " " . number_format((ceil($shipping_qty) * $shipping_mode->price) + floatval(str_replace(",","",$cart_total)), 2, '.', ',');     

            Cart::add($shipping_mode, "Shipping", $shipping_mode->quantity, $shipping_mode->price);
        }

        //get variant descriptions
        $product_variant_columns = Setting::where('company_id', $company->id)->whereIn('name', preg_filter('/^/', 'variant_',$cart->pluck('options')->pluck('product')->pluck('id')->toArray()))->orderBy('value_2')->get();

        $countries = AppSettings::where('name', 'country')->get();
        // $validatedData = $request->validate([
        //     'email' => 'required|email',
        //     'first_name' => 'required',
        //     'last_name' => 'required',
        //     'country_code' => 'required',
        //     'contact_number' => 'required|numeric|min:0',
        //     'shipping_address_1' => 'required',
        //     'city' => 'required',
        //     'zip_code' => 'required|numeric|min:0',
        //     'country' => 'required'
        // ]);

        // $order = new Order;
        // $order->email = $request->email;
        // $order->first_name = $request->first_name;
        // $order->last_name = $request->last_name;
        // $order->country_code = $request->country_code;
        // $order->contact_number = $request->contact_number;
        // $order->shipping_address_1 = $request->shipping_address_1;
        // if(!empty($request->shipping_address_2)) {
        //     $order->shipping_address_2 = $request->shipping_address_2;
        // }
        // $order->city = $request->city;
        // $order->zip_code = $request->zip_code;
        // $order->country = $request->country;

        // $company->orders()->save($order);


        //if shipping_mode is already in cart, get id object
        if(empty($shipping_mode)) {
            $shipping_mode = $cart->where('name', 'Shipping');
            //if no shipping in cart, redirect to shipping page

            if($shipping_mode->count() == 0) {
                return redirect('/'.$company->slug.'/shipping');
            } else {
                $shipping_mode = $shipping_mode->first()->id;
            }
            
        }

        $cart_itemcount = Cart::count();
        $cart_total = Cart::subtotal();
        
        return view('order_page.checkout',compact('company', 'cart', 'cart_itemcount', 'cart_total', 'product_variant_columns', 'shipping_mode', 'countries', 'payment_methods'));
    }

}
