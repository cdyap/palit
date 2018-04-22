<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Company;
use App\Product;
use App\Variant;
use App\Setting;
use App\Shipping;
use App\Order;
use App\OrderItem;
use App\AppSettings;
use Carbon\Carbon;
use Auth;
use Cart;
use Log;
use Hashids;

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
        $orders = Order::with('order_items')->where('company_id', $company->id)->orderBy('date_ordered')->get();

        return view('admin.orders',compact('sidebar', 'company','orders'));
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
    public function store($slug, Request $request)
    {
        //
        try {
            $company = Company::where('slug', $slug)->firstOrFail();
            $cart = Cart::instance($company->id)->content();
            
            //validate data
            $validatedData = $request->validate([
                'email' => 'required|email',
                'first_name' => 'required',
                'last_name' => 'required',
                'country_code' => 'required',
                'contact_number' => 'required|numeric',
                'shipping_address_1' => 'required',
                'city' => 'required',
                'zip_code' => 'required|numeric',
                'country' => 'required',
                'payment_method' => 'required',
            ]);

            $hashes = DB::table('orders')->select('hash')->get();

            //save order row
            $order = new Order;
            $order->email = $request->email;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->country_code = $request->country_code;
            $order->contact_number = $request->contact_number;
            $order->shipping_address_1 = $request->shipping_address_1;
            if(!empty($request->shipping_address_2)) {
                $order->shipping_address_2 = $request->shipping_address_2;
            }
            $order->city = $request->city;
            $order->zip_code = $request->zip_code;
            $order->country = $request->country;
            $order->payment_method = $request->payment_method;
            $order->date_ordered = new Carbon;

            //if auto-confirm updates is set
            if($company->auto_confirm_orders) {
                $order->date_confirmed = new Carbon;
            }

            //generate 10-string random hash for the order. loop repeats if generated hash already exists in database
            do {
                $random = str_random(10);
                $order->hash = $random;
            } while ($hashes->contains($random));

            //if shipping is present, insert into row
            if($cart->where('name', 'Shipping')->count() == 1) {
                $selected_shipping_mode = $cart->where('name', 'Shipping')->first();
                $order->shipping_method = $selected_shipping_mode->id->name;
                $order->shipping_price = $selected_shipping_mode->price;
            }

            $company->orders()->save($order);

            //save OrderItem for each item in cart
            foreach($cart->where('name', 'Product') as $product) {
                $order_item = new OrderItem;
                $order_item->product_id = $product->id->id;
                $order_item->product_name = $product->id->name;

                //if cart item is a variant
                if(!empty($product->options->variant)) {
                    $order_item->variant_id = $product->options->variant->id;
                    $order_item->variant_description = $product->options->description;
                }
                
                $order_item->quantity = $product->qty;
                $order_item->price = $product->price;

                $order->order_items()->save($order_item);
            }

            Cart::instance($company->id)->destroy();

            return redirect('/'.$slug.'/order/'.$order->hash)->with(['success' => "Order placed!"]);
        } catch (Exception $e) {

        }        
    }

    public function view_order($slug, $order){
        try {
            $company = Company::where('slug', $slug)->firstOrFail();
            $order = Order::where('hash', $order)->with('order_items')->firstOrFail();


            return view('order_page.view_order',compact('company', 'order'));
        } catch (Exception $e) {

        }
        
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
        
        //$request contains _token and product id. remove _token
        $product = Product::with('variants')->findOrFail(collect($request->except('_token'))->keys()[0]);

        //if $request is for a product
        if(collect($request->except('_token'))->values()->contains('no-v')) {
            Cart::instance($company->id)->add($product, "Product", 1, $product->price, ['currency' => $product->currency]);
        } else {

            //get all variants given array of variant ids passed in $request
            $variant_ids = array_keys(collect($request->except('_token'))->first());
            $variants = $product->variants->whereIn('id', $variant_ids);

            $product_variant_columns = Setting::where('company_id', $company->id)->where('name', 'variant_'.$product->id)->get();

            //for each included variant, add to cart. use cart for this company
            foreach($variants as $variant) {
                //generate description of variant
                $i = 0;
                $description = "";
                foreach($product_variant_columns as $column){
                    if(!empty($column)) {
                        $i = $i + 1;
                        if($i != 1) {
                            $description = $description . ", ";
                        }
                        $description = $description . $column->value . ": " . $variant->{"attribute_" . $i};
                    }
                }

                Cart::instance($company->id)->add($product, "Product", 1, $variant->price, ['variant' => $variant, 'currency' => $variant->product->currency, 'description' => $description]);
            }
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


        //compute for potential shipping costs per mode
        $shippings = $company->shippings;
        foreach($shippings as $mode) {
            $shipping_qty = 0;
            $cart_cost = 0.00;
            foreach($cart->where('name', 'Product') as $item) {
                $shipping_qty = $shipping_qty + ($item->id->shipping_factor * $item->qty);
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
        
        return view('order_page.shipping',compact('company', 'cart', 'cart_itemcount', 'cart_total','shippings', 'selected_shipping_mode'));
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
                $shipping_qty = $shipping_qty + ($item->id->shipping_factor * $item->qty);
            }
            $shipping_mode->total_cost = $shipping_mode->currency . " " . number_format(ceil($shipping_qty) * $shipping_mode->price, 2, '.', ',');
            $shipping_mode->view_cost = ceil($shipping_qty) * $shipping_mode->price;
            $shipping_mode->quantity = ceil($shipping_qty);
            $shipping_mode->cart_cost = $shipping_mode->currency . " " . number_format((ceil($shipping_qty) * $shipping_mode->price) + floatval(str_replace(",","",$cart_total)), 2, '.', ',');     

            Cart::add($shipping_mode, "Shipping", $shipping_mode->quantity, $shipping_mode->price);
        }

        $countries = AppSettings::where('name', 'country')->get();

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

        $country_codes = AppSettings::where('name', 'country_code')->get();
        
        return view('order_page.checkout',compact('company', 'cart', 'cart_itemcount', 'cart_total', 'shipping_mode', 'countries', 'payment_methods', 'country_codes'));
    }

    public function toggleAutoConfirm(Request $request){
        $company = Auth::user()->company;
        //parse JSON input
        $input = json_decode($request->getContent(), true);

        if($input['auto_confirm_orders'] == true) {
            $company->auto_confirm_orders = true;
            $company->save();

            return response()->json([
                'success' => true,
                'message' => 'enabled'
            ]);
        } else {
            $company->auto_confirm_orders = false;
            $company->save();

            return response()->json([
                'success' => true,
                'message' => 'disabled'
            ]);
        }
    }

}
