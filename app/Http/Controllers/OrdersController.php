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
use Notification;
use App\Notifications\OrderCreated;
use App\Jobs\NotifyUserOfOrder;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //admin-side orders
    public function confirm($hash, Request $request){
        try {
            $order = Order::with('order_items')->where('hash', $hash)->firstOrFail();

            if(!empty($order->date_paid)) {
                return back()->with(['error' => "Order " . $order->hash . " already paid"]);
            } else {
                $order->date_paid = new Carbon;
                $order->save();
                return back()->with(['success' => "Confirmed payment for order " . $order->hash]);
            }
            // dd($order->order_items);
        } 
        catch(Exception $e) {
            return back()->with(['error' => "Order not found"]);
        }
    }

    public function delete($hash, Request $request){
        try {
            //match confirmation hash with order hash
            if($hash == $request->hash) {
                $order = Order::with('order_items')->where('hash', $hash)->firstOrFail();

                //only delete unfulfilled / unpaid orders of your own company
                if (empty($order->date_fulfilled) && $order->company_id == Auth::user()->company->id) {
                    $hash = $order->hash;
                    $order->order_items()->delete();
                    $order->delete();

                    return back()->with(['success' => "Order ".$hash." deleted"]);
                } else {
                    return back()->with(['error' => "Fulfilled orders may not be deleted"]);
                }
            } else {
                return back()->with(['error' => "Order reference did not match"]);
            }
            
        } catch (Exception $e) {
            return back()->with(['error' => "Order not found"]);
        }
    }

    public function fulfill($hash, Request $request){
        try {
            $order = Order::with('order_items')->where('hash', $hash)->firstOrFail();

            if(!empty($order->date_fulfilled)) {
                return back()->with(['error' => "Order " . $order->hash . " already fulfilled"]);
            } else {
                $order_items_ids = $order->order_items->pluck('product_id');

                //get all related products
                $products = Product::with(['variants', 'variants.delivered_variants', 'delivered_products', 'variants.deliveredVariantQuantity', 'variants.deliveredVariantInitial', 'variantQuantity', 'deliveredVariantQuantity', 'deliveredProductQuantity', 'deliveredVariantInitial', 'variants.fulfilledOrders', 'fulfilledOrders'])->whereIn('id', $order_items_ids)->orderBy('name')->get();

                //set $failed_order variable = 0
                $failed_order = [];

                //go through each order item
                foreach($order->order_items as $item) {

                    //get related product of order item
                    $product = $products->where('id', $item->product_id)->first();

                    //if product found and item is a variant, process on variant-level
                    if($product && $item->is_variant) {

                        //retrieve variant object
                        $variant = $product->variants->where('id', $item->variant_id)->first();

                        //if variant object found and avail inventory >= quantity, update order item object

                        if($variant && $variant->available_inventory >= $item->quantity) {
                            $item->is_fulfilled = true;
                        } else {
                            $failed_order[] = $variant->product->name . " - " . $item->variant_description;
                        }
                    } 

                    //else if product found and item is product only, process on product-level
                    elseif ($product && !($item->is_variant)) {
                        //if product avail inventory >= item quantity, update order item object
                        if($product->available_inventory >= $item->quantity) {
                            $item->is_fulfilled = true;
                        } else {
                            $failed_order[] = $product->name;
                        }
                    }
                }

                if(count($failed_order) > 0) {
                    return back()->with(['error' => "Insufficient inventory for: <br>&nbsp;&nbsp;•&nbsp;" . collect($failed_order)->implode('<br>&nbsp;&nbsp;•&nbsp;')]);
                } else {
                    $order->date_fulfilled = new Carbon;
                    $order->save();
                    $order->order_items()->update(['is_fulfilled' => true]);
                    return back()->with(['success' => "Order " . $order->hash . " fulfilled"]);
                }
            }
            // dd($order->order_items);
        } 
        catch(Exception $e) {
            return back()->with(['error' => "Order not found"]);
        }

    }

    public function orders_cancelled(){
        $sidebar = "Orders";
        $company = Auth::user()->company;
        $orders = Order::onlyTrashed()->with('order_items_trashed', 'company', 'ordersQuantityTrashed')->where('company_id', $company->id)->orderBy('date_ordered')->paginate(10);

        $order_type = "Cancelled";

        return view('admin.orders',compact('sidebar', 'company','orders', 'title', 'order_type'));
    }

    public function orders_paid(){
        $sidebar = "Orders";
        $company = Auth::user()->company;
        $orders = Order::with('order_items', 'company', 'ordersQuantity')->where('company_id', $company->id)->orderBy('date_ordered')->where('date_paid', '<>', null)->where('date_fulfilled', null)->paginate(10);

        $order_type = "Paid";

        return view('admin.orders',compact('sidebar', 'company','orders', 'title', 'order_type'));
    }

    public function orders_shipped(){
        $sidebar = "Orders";
        $company = Auth::user()->company;
        $orders = Order::with('order_items', 'company', 'ordersQuantity')->where('company_id', $company->id)->orderBy('date_ordered')->where('date_paid', '<>', null)->where('date_fulfilled', '<>', null)->paginate(10);

        $order_type = "Shipped";

        return view('admin.orders',compact('sidebar', 'company','orders', 'title', 'order_type'));
    }

    public function orders_unpaid() {
        //
        $sidebar = "Orders";
        $company = Auth::user()->company;
        $orders = Order::with('order_items', 'company', 'ordersQuantity')->where('company_id', $company->id)->orderBy('date_ordered')->where('date_paid', null)->where('date_fulfilled', null)->paginate(10);

        $order_type = "Unpaid";

        return view('admin.orders',compact('sidebar', 'company','orders', 'title', 'order_type'));
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
            ], [
                'payment_method.required' => "Please select a payment method"
            ]);

            //Filter out illegal order quantities
        //RETRIEVE CART ITEMS !overselling_allowed WITH QTYs > AVAILABLE INVENTORY
        //get product and variant IDs and map to rowId
        $products_of_cart = $cart->where('name', 'Product')->where('options.variant.id', null)->where('id.overselling_allowed', false)->pluck('id.id', 'rowId');
        $variants_of_cart = $cart->where('name', 'Product')->where('options.variant.id', '<>', null)->where('id.overselling_allowed', false)->pluck('options.variant.id', 'rowId');
        
        $invalid_cart_items = array();

        //retrieve variant cart items with invalid QTYs
        if ($variants_of_cart->count() > 0) {
            $variants = Variant::with(['delivered_variants', 'deliveredVariantQuantity', 'deliveredVariantInitial', 'fulfilledOrders'])->select('id', 'inventory', 'product_id')->where('company_id', $company->id)->whereIn('id', $variants_of_cart)->get();

            $existing_variant_quantity = $variants->pluck('available_inventory', 'id');

            //filter out cart items with ordered quantities greater than available inventory
            $invalid_cart_items_variants = $variants_of_cart->filter(function ($item, $key) use ($cart, $existing_variant_quantity) {
                return $cart[$key]->qty > $existing_variant_quantity[$item];
            });

            if($invalid_cart_items_variants->count() > 0) {
                foreach($invalid_cart_items_variants as $rowId => $variant_id) {
                    $invalid_cart_items[$rowId] = $existing_variant_quantity->get($variant_id);
                }
            }
        }

        //retrieve product cart items with invalid QTYs
        $products = Product::with(['delivered_products', 'variantQuantity', 'deliveredVariantQuantity', 'deliveredProductQuantity', 'deliveredVariantInitial', 'fulfilledOrders'])->select('id', 'quantity', 'name', 'overselling_allowed', 'is_shipped')->where('company_id', $company->id)->whereIn('id', $products_of_cart)->orderBy('name')->get();

        //filter out cart items with ordered quantities greater than available inventory
        $invalid_cart_items_products = $products_of_cart->filter(function ($product_id, $rowId) use ($cart, $products) {
            if($cart[$rowId]->qty > $products->find($product_id)->available_inventory)
                return $product_id;
        });

        if($invalid_cart_items_products->count() > 0) {
            foreach($invalid_cart_items_products as $rowId => $product_id) {
                $invalid_cart_items[$rowId] = $products->find($product_id)->available_inventory;
            }
        }


        //FILTER OUT UNAVAILABLE CART ITEMS AND SYNC OBJECTS
        //get all products that are available and products with is_available variants
        $products_cleanup = Product::with(['collections', 'variants', 'variants.delivered_variants', 'delivered_products', 'variants.deliveredVariantQuantity', 'variants.deliveredVariantInitial', 'variantQuantity', 'deliveredVariantQuantity', 'deliveredProductQuantity', 'deliveredVariantInitial', 'variants.fulfilledOrders', 'fulfilledOrders', 'variants.pendingOrders'])
                ->where(function($query) {
                    $query->whereHas('variants', function ($query) {
                        $query->where('is_available', true);
                    })
                    ->orWhereDoesntHave('variants')->orderBy('name');
                })
                ->where('company_id', $company->id)
                ->where('is_available', true)->orderBy('name')->get();

        //get products only where !overselling_allowed and available inventory > 0 OR overselling_allowed
        $products_cleanup = collect($products_cleanup)->filter(function ($product, $key) {
            if((!$product->overselling_allowed && $product->available_inventory > 0) || ($product->overselling_allowed)) {
                return $product;
            }
        });

        //get cart for this company
        $cart_itemcount = Cart::count();
        $cart_total = Cart::subtotal();
        $product_variant_columns = $company->settings->whereIn('name', preg_filter('/^/', 'variant_',$cart->pluck('options')->pluck('product')->pluck('id')->toArray()))->sortBy('value_2');
        $unavailable_cart_items = array();
        $products_cleanup = $products_cleanup->mapWithKeys(function ($item) {
            return [$item['id'] => $item];
        });
        
        //update associated objects and remove unavailable products/variants for each cart item
        if($cart_itemcount > 0) {
            // rowId => id
            $product_ids = $cart->where('name', 'Product')->pluck('id.id', 'rowId');
            $variant_ids = $cart->where('name', 'Product')->where('options.variant.id', '<>', null)->pluck('options.variant.id', 'rowId');

            $invalid_cart_items_products = $product_ids->diff($products_cleanup->pluck('id'));

            $invalid_cart_items_variants = $variant_ids->diff($products_cleanup->pluck('variants')->flatten()->where('is_available', true)->pluck('id'));

            //merge invalid cart items and variants. remove duplicates.
            $unavailable_cart_items = array_unique(array_merge($invalid_cart_items_products->keys()->toArray(), $invalid_cart_items_variants->keys()->toArray()));

            //iterate through product cart items that aren't in unavailable_cart_items and update associated objects
            foreach($product_ids->except($unavailable_cart_items) as $rowId=>$product_id) {
                $product = $products_cleanup->get($product_id);
                if(!empty($product)) {
                    //retrieve variant also if present
                    if($variant_ids->has($rowId)) {
                        $item = Cart::get($rowId);
                        Cart::update($rowId, ['id' => $product, 'options' => ['variant' => $product->variants->where('id', $variant_ids->get($rowId))->first(), 'currency' => $item->options->currency, 'description' => $item->options->description]]);
                    } else {
                        Cart::update($rowId, ['id' => $product]);
                    }
                } else {
                    $unavailable_cart_items[] = $rowId;
                }
            }
        }

        $country_codes = AppSettings::where('name', 'country_code')->get();

        //if there are any invalid cart items OR unavailable products, return to order page displaying errors
        $invalid_cart_items_count = count($invalid_cart_items);
        if($invalid_cart_items_count > 0 || count($unavailable_cart_items) > 0) {
            return back()->with(['invalid_cart_items' => $invalid_cart_items_count, 'unavailable_cart_items' => $unavailable_cart_items, 'error' => "Some of your cart items have issues"]);
        }  else {

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
            if(!empty($request->remarks)) {
                $order->remarks = $request->remarks;
            }
            $order->city = $request->city;
            $order->zip_code = $request->zip_code;
            $order->country = $request->country;
            $order->payment_method = $request->payment_method;
            $order->date_ordered = new Carbon;

            //generate 6-string random hash for the order. loop repeats if generated hash already exists in database
            do {
                $random = strtoupper(str_random(6));
                $order->hash = $random;
            } while ($hashes->contains($random));

            //if shipping is present, insert into row
            if($cart->where('name', 'Shipping')->count() == 1) {
                $selected_shipping_mode = $cart->where('name', 'Shipping')->first();
                $order->shipping_method = $selected_shipping_mode->id->name;
                $order->shipping_price = $selected_shipping_mode->price * $selected_shipping_mode->qty;
            }

            $company->orders()->save($order);

            //save OrderItem for each item in cart
            foreach($cart->where('name', 'Product') as $product) {
                $order_item = new OrderItem;
                $order_item->product_id = $product->id->id;
                $order_item->product_name = $product->id->name;

                //if cart item is a variant
                $variant = $product->options->variant;
                if(!empty($variant)) {
                    $order_item->variant_id = $product->options->variant->id;
                    $order_item->variant_description = $product->options->description;
                }
                
                $order_item->quantity = $product->qty;
                $order_item->price = $product->price;

                $order->order_items()->save($order_item);
            }

            Cart::instance($company->id)->destroy();

            Notification::send($company->users, new OrderCreated($order));

            // return redirect('/view-order')->with(['success' => "Order placed!", 'order' => $order]);
            return view('order_page.view_order')->with(['success' => "Order placed!", 'order' => $order, 'company' => $company]);
        }
        } catch (Exception $e) {

        }        
    }

    public function view_order($order){
        try {
            $order = Order::where('hash', $order)->with('order_items')->firstOrFail();
            $company = new Company;
            $company->name = "View order";
            
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
    public function orders_index($slug){
        try {
            $company = Company::with('settings')->where('slug', $slug)->firstOrFail();

            //get all products that are available and products with is_available variants
            $products = Product::with(['collections', 'variants', 'variants.delivered_variants', 'delivered_products', 'variants.deliveredVariantQuantity', 'variants.deliveredVariantInitial', 'variantQuantity', 'deliveredVariantQuantity', 'deliveredProductQuantity', 'deliveredVariantInitial', 'variants.fulfilledOrders', 'fulfilledOrders', 'variants.pendingOrders'])
                ->where(function($query) {
                    $query->whereHas('variants', function ($query) {
                        $query->where('is_available', true);
                    })
                    ->orWhereDoesntHave('variants')->orderBy('name');
                })
                ->where('company_id', $company->id)
                ->where('is_available', true)->orderBy('name')->get();

            // dd($products);
            //get products only where !overselling_allowed and available inventory > 0 OR overselling_allowed
            $products = collect($products)->filter(function ($product, $key) {
                if((!$product->overselling_allowed && $product->available_inventory > 0) || ($product->overselling_allowed)) {
                    return $product;
                }
            });

            //get cart for this company
            $cart = Cart::instance($company->id)->content();
            $cart_itemcount = Cart::count();
            $cart_total = Cart::subtotal();
            $product_variant_columns = $company->settings->whereIn('name', preg_filter('/^/', 'variant_',$cart->pluck('options')->pluck('product')->pluck('id')->toArray()))->sortBy('value_2');
            $unavailable_cart_items = array();
            $products = $products->mapWithKeys(function ($item) {
                return [$item['id'] => $item];
            });
            
            //update associated objects and remove unavailable products/variants for each cart item
            if($cart_itemcount > 0) {
                // rowId => id
                $product_ids = $cart->where('name', 'Product')->pluck('id.id', 'rowId');
                $variant_ids = $cart->where('name', 'Product')->where('options.variant.id', '<>', null)->pluck('options.variant.id', 'rowId');


                $invalid_cart_items_products = $product_ids->diff($products->pluck('id'));

                $invalid_cart_items_variants = $variant_ids->diff($products->pluck('variants')->flatten()->where('is_available', true)->pluck('id'));

                //merge invalid cart items and variants. remove duplicates.
                $unavailable_cart_items = array_unique(array_merge($invalid_cart_items_products->keys()->toArray(), $invalid_cart_items_variants->keys()->toArray()));


                //iterate through product cart items that aren't in unavailable_cart_items and update associated objects
                foreach($product_ids->except($unavailable_cart_items) as $rowId=>$product_id) {
                    $product = $products->get($product_id);
                    if(!empty($product)) {
                        //retrieve variant also if present
                        if($variant_ids->has($rowId)) {
                            $item = Cart::get($rowId);
                            Cart::update($rowId, ['id' => $product, 'options' => ['variant' => $product->variants->where('id', $variant_ids->get($rowId))->first(), 'currency' => $item->options->currency, 'description' => $item->options->description]]);
                        } else {
                            Cart::update($rowId, ['id' => $product]);
                        }
                    } else {
                        $unavailable_cart_items[] = $rowId;
                    }
                }
            }

            return view('order_page.index',compact('company', 'cart', 'product_variant_columns', 'cart_itemcount', 'cart_total', 'products', 'unavailable_cart_items'));
        } catch (Exception $e) {
            return $e;
        }
        
    }

    public function cart($slug){
        $company = Company::where('slug', $slug)->with('products', 'collections', 'products.variants', 'products.delivered_products', 'products.delivered_variants')->firstOrFail();

        //get cart for this company
        $cart = Cart::instance($company->id)->content();

        $product_variant_columns = Setting::where('company_id', $company->id)->whereIn('name', preg_filter('/^/', 'variant_',$cart->pluck('options')->pluck('product')->pluck('id')->toArray()))->orderBy('value_2')->get();

        return view('order_page.cart',compact('company', 'cart', 'product_variant_columns'));
    }

    public function addToCart($slug, Request $request){
        $company = Company::with('settings')->where('slug', $slug)->firstOrFail();
        
        //$request contains _token and product id. remove _token
        $product = Product::with(['variants.delivered_variants', 'delivered_products', 'variants.deliveredVariantQuantity', 'variants.deliveredVariantInitial', 'variantQuantity', 'deliveredVariantQuantity', 'deliveredProductQuantity', 'deliveredVariantInitial', 'deliveredProductInitial' ,'variants.fulfilledOrders', 'fulfilledOrders'])->findOrFail(collect($request->except('_token'))->keys()[0]);

        $errors = array(); 

        //if $request is for a product
        if(collect($request->except('_token'))->values()->contains('no-v')) {
            if(!$product->overselling_allowed && $product->available_inventory < 1) {
                $errors[] = "Sorry, we ran out of stocks for " . $product->name;
            } else {
                Cart::instance($company->id)->add($product, "Product", 1, $product->price, ['currency' => $product->currency]);
            }
        } else {

            //get all variants given array of variant ids passed in $request
            $variant_ids = array_keys(collect($request->except('_token'))->first());
            $variants = $product->variants->whereIn('id', $variant_ids);
            
            $product_variant_columns = $company->settings->where('name', 'variant_'.$product->id);

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

                if(!$variant->product->overselling_allowed && $variant->available_inventory < 1) {
                    $errors[] = "Sorry, we ran out of stocks for " . $description;
                } else {
                    $variant->available_inventory = $variant->available_inventory;
                    Cart::instance($company->id)->add($product, "Product", 1, $variant->price, ['variant' => $variant, 'currency' => $variant->product->currency, 'description' => $description]);
                }
            }
        }

        if(count($errors) > 0) {
            return redirect('/'.$slug)->with(['error' => collect($errors)->implode(',')]);
        } else {
            return redirect('/'.$slug)->with(['success' => $product->name . " added to cart"]);
        }
    }

    public function removeFromCart($slug, $rowId){
        try {
            $company = Company::where('slug', $slug)->firstOrFail();
            $cart = Cart::instance($company->id)->content();
            $item = Cart::get($rowId);
            Cart::remove($rowId);

            return response()->json([
                'name' => $item->id->name,
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

    public function changeQuantity($company_id, $rowId, Request $request){
        try {
            //parse JSON input
            $input = json_decode($request->getContent(), true);

            // $company = Company::where('slug', $slug)->firstOrFail();
            $cart = Cart::instance($company_id)->content();

            if($cart->count() > 0) {
                $item = Cart::get($rowId);

                if(collect($item)->count() > 0) {
                    $item = Cart::update($rowId, $input['quantity']);    
                    return response()->json([
                        'rowId' => $rowId,
                        'success' => true,
                        'item_price' => $item->options->currency . " " . number_format($item->price*$item->qty, 2, '.', ','),
                        'cart_itemcount' => Cart::count(),
                        'cart_total' => Cart::subtotal(),
                        'currency' => $item->options->currency
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                ]);
            }
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

        //remove existing shipping selected
        foreach($cart->where('name', 'Shipping') as $shipping) {
            Cart::remove($shipping->rowId);
        }

        $cart_cost_without_shipping = Cart::subtotal(2,'.','');

        //Filter out illegal order quantities from cart
        //RETRIEVE CART ITEMS !overselling_allowed WITH QTYs > AVAILABLE INVENTORY
        //get product and variant IDs and map to rowId
        $products_of_cart = $cart->where('name', 'Product')->where('options.variant.id', null)->where('id.overselling_allowed', false)->pluck('id.id', 'rowId');
        $variants_of_cart = $cart->where('name', 'Product')->where('options.variant.id', '<>', null)->where('id.overselling_allowed', false)->pluck('options.variant.id', 'rowId');
        
        $invalid_cart_items = array();

        //retrieve variant cart items with invalid QTYs
        if ($variants_of_cart->count() > 0) {
            $variants = Variant::with(['delivered_variants', 'deliveredVariantQuantity', 'deliveredVariantInitial', 'fulfilledOrders'])->select('id', 'inventory', 'product_id')->where('company_id', $company->id)->whereIn('id', $variants_of_cart)->get();

            $existing_variant_quantity = $variants->pluck('available_inventory', 'id');

            //filter out cart items with ordered quantities greater than available inventory
            $invalid_cart_items_variants = $variants_of_cart->filter(function ($item, $key) use ($cart, $existing_variant_quantity) {
                return $cart[$key]->qty > $existing_variant_quantity[$item];
            });

            if($invalid_cart_items_variants->count() > 0) {
                foreach($invalid_cart_items_variants as $rowId => $variant_id) {
                    $invalid_cart_items[$rowId] = $existing_variant_quantity->get($variant_id);
                }
            }
        }

        //retrieve product cart items with invalid QTYs
        $products = Product::with(['delivered_products', 'variantQuantity', 'deliveredVariantQuantity', 'deliveredProductQuantity', 'deliveredVariantInitial', 'fulfilledOrders'])->select('id', 'quantity', 'name', 'overselling_allowed', 'is_shipped')->where('company_id', $company->id)->whereIn('id', $products_of_cart)->orderBy('name')->get();

        //filter out cart items with ordered quantities greater than available inventory
        $invalid_cart_items_products = $products_of_cart->filter(function ($product_id, $rowId) use ($cart, $products) {
            if($cart[$rowId]->qty > $products->find($product_id)->available_inventory)
                return $product_id;
        });

        if($invalid_cart_items_products->count() > 0) {
            foreach($invalid_cart_items_products as $rowId => $product_id) {
                $invalid_cart_items[$rowId] = $products->find($product_id)->available_inventory;
            }
        }


        //FILTER OUT UNAVAILABLE CART ITEMS AND SYNC OBJECTS
        //get all products that are available and products with is_available variants
        $products_cleanup = Product::with(['collections', 'variants', 'variants.delivered_variants', 'delivered_products', 'variants.deliveredVariantQuantity', 'variants.deliveredVariantInitial', 'variantQuantity', 'deliveredVariantQuantity', 'deliveredProductQuantity', 'deliveredVariantInitial', 'variants.fulfilledOrders', 'fulfilledOrders', 'variants.pendingOrders'])
                ->where(function($query) {
                    $query->whereHas('variants', function ($query) {
                        $query->where('is_available', true);
                    })
                    ->orWhereDoesntHave('variants')->orderBy('name');
                })
                ->where('company_id', $company->id)
                ->where('is_available', true)->orderBy('name')->get();

        //get products only where !overselling_allowed and available inventory > 0 OR overselling_allowed
        $products_cleanup = collect($products_cleanup)->filter(function ($product, $key) {
            if((!$product->overselling_allowed && $product->available_inventory > 0) || ($product->overselling_allowed)) {
                return $product;
            }
        });

        //get cart for this company
        $cart_itemcount = Cart::count();
        $cart_total = Cart::subtotal();
        $product_variant_columns = $company->settings->whereIn('name', preg_filter('/^/', 'variant_',$cart->pluck('options')->pluck('product')->pluck('id')->toArray()))->sortBy('value_2');
        $unavailable_cart_items = array();
        $products_cleanup = $products_cleanup->mapWithKeys(function ($item) {
            return [$item['id'] => $item];
        });
        
        //update associated objects and remove unavailable products/variants for each cart item
        if($cart_itemcount > 0) {
            // rowId => id
            $product_ids = $cart->where('name', 'Product')->pluck('id.id', 'rowId');
            $variant_ids = $cart->where('name', 'Product')->where('options.variant.id', '<>', null)->pluck('options.variant.id', 'rowId');

            $invalid_cart_items_products = $product_ids->diff($products_cleanup->pluck('id'));

            $invalid_cart_items_variants = $variant_ids->diff($products_cleanup->pluck('variants')->flatten()->where('is_available', true)->pluck('id'));

            //merge invalid cart items and variants. remove duplicates.
            $unavailable_cart_items = array_unique(array_merge($invalid_cart_items_products->keys()->toArray(), $invalid_cart_items_variants->keys()->toArray()));

            //iterate through product cart items that aren't in unavailable_cart_items and update associated objects
            foreach($product_ids->except($unavailable_cart_items) as $rowId=>$product_id) {
                $product = $products_cleanup->get($product_id);
                if(!empty($product)) {
                    //retrieve variant also if present
                    if($variant_ids->has($rowId)) {
                        $item = Cart::get($rowId);
                        Cart::update($rowId, ['id' => $product, 'options' => ['variant' => $product->variants->where('id', $variant_ids->get($rowId))->first(), 'currency' => $item->options->currency, 'description' => $item->options->description]]);
                    } else {
                        Cart::update($rowId, ['id' => $product]);
                    }
                } else {
                    $unavailable_cart_items[] = $rowId;
                }
            }
        }

        //if there are any invalid cart items OR unavailable products, return to order page displaying errors
        $invalid_cart_items_count = count($invalid_cart_items);
        if($invalid_cart_items_count > 0 || count($unavailable_cart_items) > 0) {
            return back()->with(['error' => 'Some of your cart items have issues', 'invalid_cart_items' => $invalid_cart_items, 'unavailable_cart_items' => $unavailable_cart_items]);
        } 

        //COMPUTE FOR SHIPPING
        //if any of cart items requires shipping, compute for shipping
        if (in_array(true, $cart->where('name', 'Product')->pluck('id.is_shipped')->toArray())) {
            //compute for potential shipping costs per shipping mode
            $shippings = $company->shippings->where('is_available', true);
            foreach($shippings as $mode) {
                $shipping_qty = 0;

                //go through each product and determine shipping cost ONLY if shipping is required
                foreach($cart->where('name', 'Product') as $item) {
                    if($item->id->is_shipped) {
                        $shipping_qty = $shipping_qty + ($item->id->shipping_factor * $item->qty);
                    }
                }
                $mode->quantity = ceil($shipping_qty);
                $mode->total_cost = $mode->currency . " " . number_format($mode->quantity * $mode->price, 2, '.', ',');
                $mode->cart_cost = $mode->currency . " " . number_format($mode->quantity * $mode->price + $cart_cost_without_shipping, 2, '.', ',');
            }
        } else {
            $shipping = new Shipping;
            $shipping->name = "None";
            $shipping->id = "None";
            $shipping->description = "Items in cart don't require shipping";
            $shipping->company_id = $company->id;
            $shipping->is_available = true;
            $shipping->currency = $company->currency;
            $shipping->price = 0;
            $shipping->total_cost = $company->currency . " " . number_format(0, 2, '.', ',');
            $shipping->cart_cost = $company->currency . " " . number_format($cart_cost_without_shipping, 2, '.', ',') ;
            $shipping->quantity = 1;

            $shippings[0] = $shipping;
            $shippings = collect($shippings);
        }

        //set selected_shipping_mode variable to null. Set if present in cart
        if($cart->where('name', 'Shipping')->count() == 1) {
            $selected_shipping_mode = $cart->where('name', 'Shipping')->first();
        } else {
            $selected_shipping_mode = null;
        }
    
        return view('order_page.shipping',compact('company', 'cart', 'cart_total','shippings', 'selected_shipping_mode' ,'invalid_cart_items'));
    }

    public function checkout($slug, Request $request){
        if ( !request()->is('/'.$slug.'/checkout') && (url()->previous() !=  url('/'.$slug.'/shipping') && url()->previous() !=  url('/'.$slug.'/checkout'))) {
            return redirect()->to('/'.$slug.'/shipping'); //Send them somewhere else
        }

        $company = Company::with('shippings')->where('slug', $slug)->firstOrFail();
        $payment_methods = $company->payment_methods;
        
        //if cart is empty, redirect back to order page
        $cart = Cart::instance($company->id)->content();
        if(Cart::count() == 0) {
            return redirect('/'.$company->slug);
        }

        $cart = Cart::instance($company->id)->content();
        $cart_total = Cart::subtotal();

        //if None is selected as shipping, add to cart
        if($request->shipping == 0 && $cart->where('name', 'Shipping')->count() == 0) {
            $shipping = new Shipping;
            $shipping->name = "None";
            $shipping->id = "0";
            $shipping->description = "Items in cart don't require shipping";
            $shipping->company_id = $company->id;
            $shipping->is_available = true;
            $shipping->currency = $company->currency;
            $shipping->price = 0;
            $shipping->total_cost = $company->currency . " " . number_format(0, 2, '.', ',');
            $shipping->view_cost = 0;
            $shipping->cart_cost = $shipping->total_cost;
            $shipping->quantity = 1;

            Cart::add($shipping, "Shipping", 1, 0, ['currency' => $company->currency]);
        }
        //if has request post data and if cart doesn't have any shipping item yet
        elseif($request->shipping) {

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

            //go through each product and determine shipping cost ONLY if shipping is required
            foreach($cart->where('name', 'Product') as $item) {
                if($item->id->is_shipped) {
                    $shipping_qty = $shipping_qty + ($item->id->shipping_factor * $item->qty);
                }
            }

            $shipping_mode->total_cost = $shipping_mode->currency . " " . number_format(ceil($shipping_qty) * $shipping_mode->price, 2, '.', ',');
            $shipping_mode->view_cost = ceil($shipping_qty) * $shipping_mode->price;
            $shipping_mode->quantity = ceil($shipping_qty);
            $shipping_mode->cart_cost = $shipping_mode->currency . " " . number_format((ceil($shipping_qty) * $shipping_mode->price) + floatval(str_replace(",","",$cart_total)), 2, '.', ',');     

            Cart::add($shipping_mode, "Shipping", $shipping_mode->quantity, $shipping_mode->price, ['currency' => $company->currency]);
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

        //Filter out illegal order quantities
        //RETRIEVE CART ITEMS !overselling_allowed WITH QTYs > AVAILABLE INVENTORY
        //get product and variant IDs and map to rowId
        $products_of_cart = $cart->where('name', 'Product')->where('options.variant.id', null)->where('id.overselling_allowed', false)->pluck('id.id', 'rowId');
        $variants_of_cart = $cart->where('name', 'Product')->where('options.variant.id', '<>', null)->where('id.overselling_allowed', false)->pluck('options.variant.id', 'rowId');
        
        $invalid_cart_items = array();

        //retrieve variant cart items with invalid QTYs
        if ($variants_of_cart->count() > 0) {
            $variants = Variant::with(['delivered_variants', 'deliveredVariantQuantity', 'deliveredVariantInitial', 'fulfilledOrders'])->select('id', 'inventory', 'product_id')->where('company_id', $company->id)->whereIn('id', $variants_of_cart)->get();

            $existing_variant_quantity = $variants->pluck('available_inventory', 'id');

            //filter out cart items with ordered quantities greater than available inventory
            $invalid_cart_items_variants = $variants_of_cart->filter(function ($item, $key) use ($cart, $existing_variant_quantity) {
                return $cart[$key]->qty > $existing_variant_quantity[$item];
            });

            if($invalid_cart_items_variants->count() > 0) {
                foreach($invalid_cart_items_variants as $rowId => $variant_id) {
                    $invalid_cart_items[$rowId] = $existing_variant_quantity->get($variant_id);
                }
            }
        }

        //retrieve product cart items with invalid QTYs
        $products = Product::with(['delivered_products', 'variantQuantity', 'deliveredVariantQuantity', 'deliveredProductQuantity', 'deliveredVariantInitial', 'fulfilledOrders'])->select('id', 'quantity', 'name', 'overselling_allowed', 'is_shipped')->where('company_id', $company->id)->whereIn('id', $products_of_cart)->orderBy('name')->get();

        //filter out cart items with ordered quantities greater than available inventory
        $invalid_cart_items_products = $products_of_cart->filter(function ($product_id, $rowId) use ($cart, $products) {
            if($cart[$rowId]->qty > $products->find($product_id)->available_inventory)
                return $product_id;
        });

        if($invalid_cart_items_products->count() > 0) {
            foreach($invalid_cart_items_products as $rowId => $product_id) {
                $invalid_cart_items[$rowId] = $products->find($product_id)->available_inventory;
            }
        }


        //FILTER OUT UNAVAILABLE CART ITEMS AND SYNC OBJECTS
        //get all products that are available and products with is_available variants
        $products_cleanup = Product::with(['collections', 'variants', 'variants.delivered_variants', 'delivered_products', 'variants.deliveredVariantQuantity', 'variants.deliveredVariantInitial', 'variantQuantity', 'deliveredVariantQuantity', 'deliveredProductQuantity', 'deliveredVariantInitial', 'variants.fulfilledOrders', 'fulfilledOrders', 'variants.pendingOrders'])
                ->where(function($query) {
                    $query->whereHas('variants', function ($query) {
                        $query->where('is_available', true);
                    })
                    ->orWhereDoesntHave('variants')->orderBy('name');
                })
                ->where('company_id', $company->id)
                ->where('is_available', true)->orderBy('name')->get();

        //get products only where !overselling_allowed and available inventory > 0 OR overselling_allowed
        $products_cleanup = collect($products_cleanup)->filter(function ($product, $key) {
            if((!$product->overselling_allowed && $product->available_inventory > 0) || ($product->overselling_allowed)) {
                return $product;
            }
        });

        //get cart for this company
        $cart_itemcount = Cart::count();
        $cart_total = Cart::subtotal();
        $product_variant_columns = $company->settings->whereIn('name', preg_filter('/^/', 'variant_',$cart->pluck('options')->pluck('product')->pluck('id')->toArray()))->sortBy('value_2');
        $unavailable_cart_items = array();
        $products_cleanup = $products_cleanup->mapWithKeys(function ($item) {
            return [$item['id'] => $item];
        });
        
        //update associated objects and remove unavailable products/variants for each cart item
        if($cart_itemcount > 0) {
            // rowId => id
            $product_ids = $cart->where('name', 'Product')->pluck('id.id', 'rowId');
            $variant_ids = $cart->where('name', 'Product')->where('options.variant.id', '<>', null)->pluck('options.variant.id', 'rowId');

            $invalid_cart_items_products = $product_ids->diff($products_cleanup->pluck('id'));

            $invalid_cart_items_variants = $variant_ids->diff($products_cleanup->pluck('variants')->flatten()->where('is_available', true)->pluck('id'));

            //merge invalid cart items and variants. remove duplicates.
            $unavailable_cart_items = array_unique(array_merge($invalid_cart_items_products->keys()->toArray(), $invalid_cart_items_variants->keys()->toArray()));

            //iterate through product cart items that aren't in unavailable_cart_items and update associated objects
            foreach($product_ids->except($unavailable_cart_items) as $rowId=>$product_id) {
                $product = $products_cleanup->get($product_id);
                if(!empty($product)) {
                    //retrieve variant also if present
                    if($variant_ids->has($rowId)) {
                        $item = Cart::get($rowId);
                        Cart::update($rowId, ['id' => $product, 'options' => ['variant' => $product->variants->where('id', $variant_ids->get($rowId))->first(), 'currency' => $item->options->currency, 'description' => $item->options->description]]);
                    } else {
                        Cart::update($rowId, ['id' => $product]);
                    }
                } else {
                    $unavailable_cart_items[] = $rowId;
                }
            }
        }

        $country_codes = AppSettings::where('name', 'country_code')->get();

        //if there are any invalid cart items OR unavailable products, return to order page displaying errors
        $invalid_cart_items_count = count($invalid_cart_items);
        if($invalid_cart_items_count > 0 || count($unavailable_cart_items) > 0) {
            $error = "Some of your cart items have issues";
            \Session::flash('invalid_cart_items', $invalid_cart_items_count);
            \Session::flash('unavailable_cart_items', $unavailable_cart_items);
            \Session::flash('error', 'Some of your cart items have issues');
            
            \Session::flash('error', 'Some of your cart items have issues');
            return view('order_page.checkout',compact('company', 'cart', 'cart_itemcount', 'cart_total', 'shipping_mode', 'countries', 'payment_methods', 'country_codes', 'invalid_cart_items', 'unavailable_cart_items'));
        } 
        
        return view('order_page.checkout',compact('company', 'cart', 'cart_itemcount', 'cart_total', 'shipping_mode', 'countries', 'payment_methods', 'country_codes'));
    }

    // public function toggleAutoConfirm(Request $request){
    //     $company = Auth::user()->company;
    //     //parse JSON input
    //     $input = json_decode($request->getContent(), true);

    //     if($input['auto_confirm_orders'] == true) {
    //         $company->auto_confirm_orders = true;
    //         $company->save();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'enabled'
    //         ]);
    //     } else {
    //         $company->auto_confirm_orders = false;
    //         $company->save();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'disabled'
    //         ]);
    //     }
    // }

    public function check_order_status(){
        return view('home_check_order_status');
    }

    public function find_order(Request $request){
        $validatedData = $request->validate([
            'email' => 'required|email',
            'hash' => 'required'
        ]);

        $email = $request->email;
        $hash = $request->hash;

        $order = Order::with('order_items', 'company', 'ordersQuantity')->where('email', $email)->where('hash', $hash)->first();

        if (!empty($order)) {
            // $order = Order::where('hash', $order)->with('order_items')->firstOrFail();
            $company = new Company;
            $company->name = "View order";
            
            return view('order_page.view_order',compact('company', 'order'));

            // return redirect('/order/'.$hash);
        } else {
            return back()->with(['error' => "Order " . $hash . " not found for " . $email])->withInput($request->input());
        }
    }

    public function redirect_to_home($company, Request $request){
        return redirect('/'.$company);
    }

    public function redirect_to_shipping($company, Request $request){
        return redirect('/'.$company.'/shipping');
    }
}
