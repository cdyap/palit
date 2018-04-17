<?php

namespace App\Http\Controllers;
use App\Product;
use App\Variant;
use App\Delivery;
use App\Setting;
use App\DeliveredVariant;
use App\DeliveredProduct;
use Auth;
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
        $products = Product::with(['variants.delivered_variants', 'delivered_products'])->where('company_id', $company->id)->orderBy('name')->get();
        $deliveries = Delivery::with(['delivered_variants.variant', 'delivered_variants.product', 'delivered_products.product'])->where('company_id', $company->id)->orderBy('expected_arrival')->get();
        $completed_deliveries = $deliveries->where('is_received', true);
        $pending_deliveries = $deliveries->where('is_received', false);
        //get all variant columns of all products where name is in all plucked product IDs prefixed with 'variant_'
        $product_variant_columns = Setting::where('company_id', Auth::user()->company->id)->whereIn('name', preg_filter('/^/', 'variant_', $products->pluck('id')->toArray()))->orderBy('value_2')->get();

        $today = Carbon::now();

        return view('admin.inventory',compact('sidebar', 'products', 'deliveries', 'pending_deliveries', 'completed_deliveries', 'today', 'product_variant_columns'));
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
        $title = "Add delivery";
        $products = Product::with('variants')->where('company_id', $company->id)->orderBy('name')->get();
        return view('admin.inventory_new',compact('sidebar', 'products', 'title', 'delivery'));
    }

    public function getProduct(Request $request){
        $company = Auth::user()->company;
        try {
            $product = Product::with('variants')->find($request->product_id);
            $has_variants = false;
            $variant_columns = [];

            if($product->variants()->count() > 0) {
                $has_variants = true;
                $variant_columns = $product->variant_columns()->sortBy('value_2');
            }

            return response()->json([
                'success' => 1,
                'product' => $product,
                'total_inventory' => $product->total_inventory,
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
            $delivery = new Delivery;
            $delivery->supplier = $request->supplier;
            $delivery->expected_arrival = $request->expected_arrival;
            $delivery->is_received = false;
            $delivery->company_id = Auth::user()->company->id;
            $delivery->save();

            if (!empty($request->variant)) {
                foreach($request->variant as $key => $value) {
                    $variant = Variant::with('product')->findOrFail($key);

                    $delivered_variant = new DeliveredVariant;
                    $delivered_variant->quantity = $value;
                    $delivered_variant->variant_id = $variant->id;
                    $delivered_variant->product_id = $variant->product->id;
                    // dd($delivered_variant);
                    $delivery->delivered_variants()->save($delivered_variant);
                }
            }

            if (!empty($request->product)) {
                foreach($request->product as $key => $value) {
                    $product = Product::findOrFail($key);
                    $delivered_product = new DeliveredProduct;
                    $delivered_product->quantity = $value;
                    $delivered_product->product_id = $product->id;
                    // dd($delivered_variant);
                    $delivery->delivered_products()->save($delivered_product);
                }
            }
        } catch (Exception $e) {
            return back()->with(['error' => "Delivery added!"])
                    ->withInput($request->input());
        }
        
        
        return redirect('/inventory')->with(['success' => "Delivery added!"]);
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
        $delivery = Delivery::with('delivered_products', 'delivered_variants')->findOrFail($id);
        $delivery->delivered_variants()->delete();
        $delivery->delivered_products()->delete();
        $delivery->delete();

        return redirect('/inventory')->with(['success' => "Delivery from ".$delivery->supplier." deleted!"]);
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

    public function receive_delivery($delivery_slug, Request $request){

        //if delivered_variants are present
        if (!empty($request->delivered_variant)) {
            $delivered_variants = DeliveredVariant::whereIn('id',array_keys($request->delivered_variant))->get();
            $delivery = $delivered_variants->first()->delivery;
            $error_message = [];
            $i = 0;

            foreach($delivered_variants as $delivered_variant) {
                //set delivered_quantity variable to existing delivered_quantity attribute of delivered_variant
                $delivered_quantity = $delivered_variant->delivered_quantity;

                //add delivered_quantity input to delivered_quantity attribute
                $delivered_quantity = $delivered_quantity + $request->delivered_variant[$delivered_variant->id]['delivered_quantity'];

                //delivered_quantity attribute must not be greater than ordered quantity
                if ($delivered_quantity > $delivered_variant->quantity) {
                    $error_message[$i++] = "Received quantity cannot be greater than ordered quantity.";
                } else {
                    $delivered_variant->delivered_quantity = $delivered_quantity;
                    if ($delivered_variant->delivered_quantity == $delivered_variant->delivered_quantity) {
                        $delivered_variant->is_delivered = true;
                    } else {
                        $delivered_variant->is_delivered = false;
                    }
                    $delivered_variant->save();
                }
            }
        }

        //if delivered_products are present
        if (!empty($request->delivered_product)) {
            $delivered_products = DeliveredProduct::whereIn('id',array_keys($request->delivered_product))->get();
            $delivery = $delivered_products->first()->delivery;

            $error_message = [];
            $i = 0;

            foreach($delivered_products as $delivered_product) {
                //set delivered_quantity variable to existing delivered_quantity attribute of delivered_variant
                $delivered_quantity = $delivered_product->delivered_quantity;

                //add delivered_quantity input to delivered_quantity attribute
                $delivered_quantity = $delivered_quantity + $request->delivered_product[$delivered_product->id]['delivered_quantity'];

                //delivered_quantity attribute must not be greater than ordered quantity
                if ($delivered_quantity > $delivered_product->quantity) {
                    $error_message[$i++] = "Received quantity cannot be greater than ordered quantity.";
                } else {
                    $delivered_product->delivered_quantity = $delivered_quantity;
                    if ($delivered_product->delivered_quantity == $delivered_product->delivered_quantity) {
                        $delivered_product->is_delivered = true;
                    } else {
                        $delivered_product->is_delivered = false;
                    }
                    $delivered_product->save();
                }
            }
        }

        //if delivery's quantity = delivered_quantity, set delivery->is_received = true
        if(($delivery->delivered_products->sum('quantity') + $delivery->delivered_variants->sum('quantity')) == ($delivery->delivered_products->sum('delivered_quantity') + $delivery->delivered_variants->sum('delivered_quantity'))) {
            $delivery->is_received = true;
            $delivery->save();
        } else {
            $delivery->is_received = false;
            $delivery->save();
        }
        return back()->with(['success' => "Received items!"]);

    }
}
