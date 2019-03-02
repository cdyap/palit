<?php

namespace App\Http\Controllers;
use App\Product;
use App\Variant;
use App\Order;
use App\Delivery;
use App\Setting;
use App\DeliveredVariant;
use App\DeliveredProduct;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware('check_session');
    }
    
    public function index()
    {
        //
        $sidebar = "Inventory";
        $company = Auth::user()->company;
        $products = Product::with(['variants.delivered_variants', 'delivered_products', 'variants.deliveredVariantQuantity', 'variants.deliveredVariantInitial', 'variantQuantity', 'deliveredVariantQuantity', 'deliveredProductQuantity', 'deliveredVariantInitial', 'deliveredProductInitial' ,'variants.fulfilledOrders', 'fulfilledOrders'])->where('company_id', $company->id)->orderBy('name')->get();
        $deliveries = Delivery::with(['delivered_products', 'delivered_variants','deliveredVariantsCount', 'deliveredProductsCount', 'delivered_variants.variant', 'delivered_variants.product', 'delivered_products.product'])->where('company_id', $company->id)->orderBy('expected_arrival')->get();
        $completed_deliveries = $deliveries->where('is_received', true);
        $pending_deliveries = $deliveries->where('is_received', false);
        
        //get all variant columns of all products where name is in all plucked product IDs prefixed with 'variant_'
        $product_variant_columns = Setting::where('company_id', Auth::user()->company->id)->whereIn('name', preg_filter('/^/', 'variant_', $products->pluck('id')->toArray()))->orderBy('value_2')->get();

        $product_ids_with_variant_columns = $product_variant_columns->unique('name')->pluck('name')->map(function ($item, $key) {
            return substr($item,8);
        });

        $products_with_problems = Product::doesntHave('variants')->whereIn('id', $product_ids_with_variant_columns)->orderBy('name')->get();


        $today = Carbon::now();

        $unpaid_orders = DB::table('order_items')
                                ->whereIn('order_id', function($query) use ($company)
                                {
                                    $query->select(DB::raw("id"))
                                          ->from('orders')
                                          ->whereRaw('date_paid IS NULL')
                                          ->where('company_id', $company->id)
                                          ->whereRaw('deleted_at IS NULL')
                                          ->whereRaw('date_fulfilled IS NULL');
                                })
                                ->get();

        $paid_orders = DB::table('order_items')
                                ->whereIn('order_id', function($query) use ($company)
                                {
                                    $query->select(DB::raw("id"))
                                          ->from('orders')
                                          ->whereRaw('date_paid IS NOT NULL')
                                          ->where('company_id', $company->id)
                                          ->whereRaw('deleted_at IS NULL')
                                          ->whereRaw('date_fulfilled IS NULL');
                                })
                                ->get();

        return view('admin.inventory',compact('sidebar', 'products', 'deliveries', 'pending_deliveries', 'completed_deliveries', 'today', 'product_variant_columns', 'paid_orders', 'unpaid_orders', 'products_with_problems'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $sidebar = "Inventory";
        $company = Auth::user()->company;
        $title = "Add stocks";
        $products_with_variants = Product::with(['variants.delivered_variants', 'delivered_products', 'variants.deliveredVariantQuantity', 'variants.deliveredVariantInitial', 'variantQuantity', 'deliveredVariantQuantity', 'deliveredProductQuantity', 'deliveredVariantInitial', 'deliveredProductInitial' ,'variants.fulfilledOrders', 'fulfilledOrders'])->has('variants')->where('company_id', $company->id)->orderBy('name')->get();
        $products_wo_variants = Product::with(['variants.delivered_variants', 'delivered_products', 'variants.deliveredVariantQuantity', 'variants.deliveredVariantInitial', 'variantQuantity', 'deliveredVariantQuantity', 'deliveredProductQuantity', 'deliveredVariantInitial', 'deliveredProductInitial' ,'variants.fulfilledOrders', 'fulfilledOrders'])->doesntHave('variants')->where('company_id', $company->id)->orderBy('name')->get();

        //get all variant columns of all products where name is in all plucked product IDs prefixed with 'variant_'
        $product_variant_columns = Setting::where('company_id', Auth::user()->company->id)->whereIn('name', preg_filter('/^/', 'variant_', $products_with_variants->pluck('id')->toArray()))->orderBy('value_2')->get();

        $product_ids_with_variant_columns = $product_variant_columns->unique('name')->pluck('name')->map(function ($item, $key) {
            return substr($item,8);
        });

        $products_with_problems = Product::doesntHave('variants')->whereIn('id', $product_ids_with_variant_columns)->orderBy('name')->get();

        $unpaid_orders = DB::table('order_items')
                                ->whereIn('order_id', function($query) use ($company)
                                {
                                    $query->select(DB::raw("id"))
                                          ->from('orders')
                                          ->whereRaw('date_paid IS NULL')
                                          ->where('company_id', $company->id)
                                          ->whereRaw('deleted_at IS NULL')
                                          ->whereRaw('date_fulfilled IS NULL');
                                })
                                ->get();

        $paid_orders = DB::table('order_items')
                                ->whereIn('order_id', function($query) use ($company)
                                {
                                    $query->select(DB::raw("id"))
                                          ->from('orders')
                                          ->whereRaw('date_paid IS NOT NULL')
                                          ->where('company_id', $company->id)
                                          ->whereRaw('deleted_at IS NULL')
                                          ->whereRaw('date_fulfilled IS NULL');
                                })
                                ->get();

        return view('admin.inventory_new',compact('sidebar', 'products_with_variants', 'products_wo_variants', 'title', 'delivery', 'paid_orders', 'unpaid_orders', 'product_variant_columns'));
    }

    public function getProduct(Request $request){
        // $company = Auth::user()->company;
        try {
            $product = Product::with(['variants.delivered_variants', 'delivered_products', 'variants.deliveredVariantQuantity', 'variants.deliveredVariantInitial', 'variantQuantity', 'deliveredVariantQuantity', 'deliveredProductQuantity', 'deliveredVariantInitial', 'variants.fulfilledOrders', 'fulfilledOrders'])->find($request->product_id);
            $has_variants = false;
            $variant_columns = [];

            if($product->variants->count() > 0) {
                $has_variants = true;
                $variant_columns = $product->variant_columns()->sortBy('value_2');
            }

            return response()->json([
                'success' => 1,
                'product' => $product,
                'available_inventory' => $product->available_inventory,
                'incoming_inventory' => $product->incoming_inventory,
                'has_variants' => $has_variants,
                'variant_columns' => $variant_columns,
                'incoming' => $product->incoming
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => 0
            ]);
        }
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
        $validatedData = $request->validate([
            'expected_arrival' => 'required|date',
            'supplier' => 'required',
        ]);

        try {
            //filter out empty / 0 stocks entered in input fields
            $filtered_variants = collect($request->variant)->filter(function ($value, $key) {
                return $value > 0;
            });
            $filtered_products = collect($request->product)->filter(function ($value, $key) {
                return $value > 0;
            });

            //error boolean check
            $errors_invalid_variants = false;
            $errors_invalid_products = false;

            if (!empty($filtered_variants)) {
                $variants = Variant::whereIn('id',$filtered_variants->keys())->where('company_id', Auth::user()->company->id)->select('id', 'product_id')->get();
                
                //if variants from query == request->variants, run next loop, else declare error variable as true.
                if($variants->count() != $filtered_variants->count()) {
                    $errors_invalid_variants = true;
                }
            }

            if (!empty($filtered_products)) {
                $products = Product::whereIn('id',$filtered_products->keys())->where('company_id', Auth::user()->company->id)->select('id')->get();
                
                //if products from query == request->products, run next loop, else declare error variable as true.
                if($products->count() != $filtered_products->count()) {
                    $errors_invalid_variants = true;
                }
            }


            if(!$errors_invalid_variants && !$errors_invalid_products) {
                $delivery = new Delivery;
                $delivery->supplier = $request->supplier;
                $delivery->expected_arrival = $request->expected_arrival;
                $delivery->is_received = false;
                $delivery->company_id = Auth::user()->company->id;
                $delivery->save();

                if($filtered_variants->count() > 0) {
                    foreach($filtered_variants as $key => $value) {
                        $variant = $variants->firstWhere('id', $key);

                        if(!empty($variant)) {
                            $delivered_variant = new DeliveredVariant;
                            $delivered_variant->quantity = $value;
                            $delivered_variant->variant_id = $variant->id;
                            $delivered_variant->product_id = $variant->product->id;

                            $delivery->delivered_variants()->save($delivered_variant);
                        } else {
                            $errors_invalid_variants = $errors_invalid_variants + 1;
                        }         
                    }
                }

                if($filtered_products->count() > 0) {
                    foreach($filtered_products as $key => $value) {
                        $product = $products->firstWhere('id', $key);

                        if(!empty($variant)) {
                            $delivered_product = new DeliveredProduct;
                            $delivered_product->quantity = $value;
                            $delivered_product->product_id = $product->id;

                            $delivery->delivered_products()->save($delivered_product);
                        } else {
                            $errors_invalid_products = $errors_invalid_products + 1;
                        }
                    }
                }

                return redirect('/inventory')->with(['success' => "Stocks added"]);
            } else {
                return back()->with(['error' => "Products/variants entered for restocking are not yours"]);
            }
        } catch (Exception $e) {
            return back()->with(['error' => "Delivery added!"])
                    ->withInput($request->input());
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
        try {
            $delivery = Delivery::with('delivered_products', 'delivered_variants')->where('id', $id)->where('company_id', Auth::user()->company->id)->firstOrFail();
            $delivery->delivered_variants()->delete();
            $delivery->delivered_products()->delete();
            $delivery->delete();

            return redirect('/inventory')->with(['success' => "Delivery from ".$delivery->supplier." deleted"]);
        } catch (Exception $e) {
            return back()->with(['error' => "Something went wrong"]);
        }
        
    }

    public function view_delivery($delivery_slug){
        $delivery = Delivery::with(['delivered_variants.variant', 'delivered_variants.product', 'delivered_products.product', 'delivery_logs'])->where('slug', $delivery_slug)->firstOrFail();
        $sidebar = "Inventory";
        $company = Auth::user()->company;
        $title = "Delivery from " . $delivery->supplier;

        $products = Product::with(['variants.delivered_variants', 'delivered_products'])->where('company_id', $company->id)->orderBy('name')->get();

        //get all variant columns of all products where name is in all plucked product IDs prefixed with 'variant_'
        $product_variant_columns = Setting::where('company_id', Auth::user()->company->id)->whereIn('name', preg_filter('/^/', 'variant_', $products->pluck('id')->toArray()))->orderBy('value_2')->get();

        return view('admin.delivery_show',compact('sidebar', 'company', 'title', 'delivery', 'product_variant_columns'));
    }

    public function receive_delivery($delivery_id, Request $request){

        //Get parameters with more than 0
        $filtered_products = collect($request->delivered_product)->filter(function ($value, $key) {
            return $value > 0;
        });

        $filtered_variants = collect($request->delivered_variant)->filter(function ($value, $key) {
            return $value > 0;
        });

        $error_message = array();

        //if delivered_products are present
        if ($filtered_products->count() > 0) {
            $delivered_products = DeliveredProduct::whereIn('id',$filtered_products->keys())->get();

            $error_message = [];

            foreach($delivered_products as $delivered_product) {
                //add input value to current delivered_quantity attribute of delivered_product
                $delivered_product->delivered_quantity = $delivered_product->delivered_quantity + $filtered_products->get($delivered_product->id);

                //delivered_quantity attribute must not be greater than ordered quantity
                if($delivered_product->delivered_quantity > $delivered_product->quantity) {
                    $error_message[] = "Received quantity cannot be greater than ordered quantity";
                } else {
                    $delivered_product->save();
                }
            }
        }

        //if delivered_variants are present
        if ($filtered_variants->count() > 0) {
            $delivered_variants = DeliveredVariant::whereIn('id',$filtered_variants->keys())->get();

            $error_message = [];

            foreach($delivered_variants as $delivered_variant) {
                //add input value to current delivered_quantity attribute of delivered_product
                $delivered_variant->delivered_quantity = $delivered_variant->delivered_quantity + $filtered_variants->get($delivered_variant->id);

                //delivered_quantity attribute must not be greater than ordered quantity
                if($delivered_variant->delivered_quantity > $delivered_variant->quantity) {
                    $error_message[] = "Received quantity cannot be greater than ordered quantity";
                } else {
                    $delivered_variant->save();
                }
            }
        }

        //get updated counts from the database
        $delivery = Delivery::with('delivered_variants', 'delivered_products')->findOrFail($delivery_id);

        //if delivery's quantity = delivered_quantity, set delivery->is_received = true
        if(($delivery->delivered_products->sum('quantity') + $delivery->delivered_variants->sum('quantity')) == ($delivery->delivered_products->sum('delivered_quantity') + $delivery->delivered_variants->sum('delivered_quantity'))) {
            $delivery->is_received = true;
            $delivery->save();
            return back()->with(['success' => "Delivery from ".$delivery->supplier." complete"]);
        } else {
            $delivery->is_received = false;
            $delivery->save();
            return back()->with(['success' => "Received ".($filtered_variants->sum() + $filtered_products->sum())." item/s from ".$delivery->supplier]);
        }

    }
}
