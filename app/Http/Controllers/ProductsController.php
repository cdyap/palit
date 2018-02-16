<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use \App\Product;
use \App\Variant;
use \App\Setting;
use \App\AppSettings;
use Auth;
use Illuminate\Database\Query\Builder;

class ProductsController extends Controller
{
    //
	public function index($slug){
    	$sidebar = "Products";
    	$title = "All products";
    	$products = Product::get();

    	$available_products = $products->where('is_available', true);
    	$unavailable_products = $products->where('is_available', false);

    	return view('admin.products',compact('sidebar', 'title', 'products', 'available_products', 'unavailable_products'));
    }

    public function new($slug){
    	$sidebar = "Products";
    	$title = "New product";

    	$currencies = AppSettings::where('name', 'currency')->get();

    	return view('admin.products_new',compact('sidebar', 'title', 'currencies'));
    }

    public function edit($slug, $product_slug){
    	try {

	    	$product = Product::where('slug', $product_slug)->firstOrFail();
	    	$sidebar = "Products";
	    	$title = "Edit ". $product->name;

	    	$currencies = AppSettings::where('name', 'currency')->get();
	    	return view('admin.products_edit',compact('sidebar', 'title', 'product', 'currencies'));
		} catch (Exception $e) {
			$product = Product::where('slug', $product_slug)->firstOrFail();
	    	$sidebar = "Products";
	    	$title = "Edit ". $product->name;

			return back()->with(['error' => $e->getMessage(), 'product' => $product, 'sidebar' => $sidebar, 'title' => $title]);
		}
    }

    public function update($slug, $product_slug, Request $request){
    	try {
    		$product = Product::where('slug', $product_slug)->firstOrFail();
	    	$sidebar = "Products";
	    	$title = "Edit ". $product->name;
	    	$validatedData = $request->validate([
		        'name' => 'required|unique:products,name,'.$product->id,
		        'description' => 'required',
		        'currency' => 'required',
		        'price' => 'sometimes|numeric|min:0',
		        'quantity' => 'sometimes|nullable|numeric|min:0',
		        'SKU' => Rule::unique('products')->where(function ($query) {
				    return $query->where('company_id', Auth::user()->company->id);
				})
		    ]);

		    if (!empty($request->price)) {
		    	$product->variants()->update(['price'=>$request->price]);
		    }

		    $product->update($request->all());

		    if(empty($request->is_shipped)) {
		    	$product->is_shipped = false;
		    	$product->save();
		    }

			return redirect('/'.Auth::user()->url().'/products/'.$product->slug)->with(['success' => " edited!", 'emphasize' => $product->name]);		    

    	} catch (Exception $e) {
    		$product = Product::where('slug', $product_slug)->firstOrFail();
	    	$sidebar = "Products";
	    	$title = "Edit ". $product->name;

    		return back()->with(['error' => $e->getMessage()]);
    	}
    }

    public function delete($slug, $product_slug, Request $request){
    	try {
	    	$product = Product::where('slug', $product_slug)->firstOrFail();
	    	$product->delete();

	    	return redirect('/'.Auth::user()->url().'/products')->with(['success' => " deleted!", 'emphasize' => $product->name]);
		} catch (Exception $e) {
			return back()->with(['error' => $e->getMessage()]);
		}
    }

	public function save(Request $request){
		$validatedData = $request->validate([
	        'name' => 'required|unique:products',
	        'description' => 'required',
	        'currency' => 'required',
	        'price' => 'numeric|min:0',
	        'quantity' => 'nullable|numeric|min:0',
	        'SKU' => Rule::unique('products')->where(function ($query) {
			    return $query->where('company_id', Auth::user()->company->id);
			})
	    ]);

		$product = new Product;

		$product->name = $request->name;
		$product->description = $request->description;
		$product->company_id = Auth::id();
		$product->price = $request->price;
		$product->currency = $request->currency;
		$product->SKU = $request->SKU;
		$product->is_available = true;
		$product->quantity = $request->quantity;
		if (!empty($request->is_shipped)){
			$product->is_shipped = true;
		}

		$product->save();

		if (!empty($request->attribute_1)) {
			$variant = new Setting;

			$variant->company_id = Auth::user()->company->id;
			$variant->name = "variant_" . $product->id;
			$variant->value = $request->attribute_1;
			$variant->value_2 = "attribute_1";

			$variant->save();
		}
		if (!empty($request->attribute_2)) {
			$variant = new Setting;

			$variant->company_id = Auth::user()->company->id;
			$variant->name = "variant_" . $product->id;
			$variant->value = $request->attribute_2;
			$variant->value_2 = "attribute_2";

			$variant->save();
		}
		if (!empty($request->attribute_3)) {
			$variant = new Setting;

			$variant->company_id = Auth::user()->company->id;
			$variant->name = "variant_" . $product->id;
			$variant->value = $request->attribute_3;
			$variant->value_2 = "attribute_3";

			$variant->save();
		}
		if (!empty($request->attribute_4)) {
			$variant = new Setting;

			$variant->company_id = Auth::user()->company->id;
			$variant->name = "variant_" . $product->id;
			$variant->value = $request->attribute_4;
			$variant->value_2 = "attribute_4";

			$variant->save();
		}
		if (!empty($request->attribute_5)) {
			$variant = new Setting;

			$variant->company_id = Auth::user()->company->id;
			$variant->name = "variant_" . $product->id;
			$variant->value = $request->attribute_5;
			$variant->value_2 = "attribute_5";

			$variant->save();
		}
		return redirect('/'.Auth::user()->url().'/products')->with(['success' => " added!", 'emphasize' => $product->name]);
	}

	public function show($slug, $product_slug){
		$product = Product::where('slug', $product_slug)->with('variants')->get()->first();
		$title = $product->name;
		$sidebar = "Products";

		return view('admin.products_show',compact('sidebar', 'product', 'title'));
	}

	public function addVariantColumn($slug, $product_slug, Request $request){
		try {
			$product = Product::where('slug', $product_slug)->with('variants')->firstOrFail();
			$title = $product->name;
			$sidebar = "Products";
			$count_variants_added = 0;

			if (!empty($request->attribute_1)) {
				$check_if_exists = Setting::where('name', 'variant_' . $product->id)->where('company_id', Auth::user()->company->id)->where('value_2', "attribute_1")->get();

				if ($check_if_exists->count() == 0) {
					$new_variant_column = new Setting;
					$new_variant_column->company_id = Auth::user()->company->id;
					$new_variant_column->name = "variant_" . $product->id;
					$new_variant_column->value = $request->attribute_1;

					$attr_no = $count_variants_added + 1;
					$new_variant_column->value_2 = strval("attribute_1");
					try {
						$new_variant_column->save();
						$count_variants_added = $count_variants_added + 1;
					} catch (Exception $e) {

					}
				} else {
					$check_if_exists->first()->value = $request->attribute_1;
					$check_if_exists->first()->save();
				}
			} else {
				Setting::where('name', 'variant_' . $product->id)->where('company_id', Auth::user()->company->id)->where('value_2', "attribute_1")->delete();

				//if no attribute 1, delete all variants
				$product->variants()->delete();
				$product->quantity = 0;
				$product->save();
			}

			if (!empty($request->attribute_2)) {
				$check_if_exists = Setting::where('name', 'variant_' . $product->id)->where('company_id', Auth::user()->company->id)->where('value_2', "attribute_2")->get();

				if ($check_if_exists->count() == 0) {
					$new_variant_column = new Setting;
					$new_variant_column->company_id = Auth::user()->company->id;
					$new_variant_column->name = "variant_" . $product->id;
					$new_variant_column->value = $request->attribute_2;

					$attr_no = $count_variants_added + 1;
					$new_variant_column->value_2 = strval("attribute_2");
					try {
						$new_variant_column->save();
						$count_variants_added = $count_variants_added + 1;
					} catch (Exception $e) {

					}
				} else {
					$check_if_exists->first()->value = $request->attribute_2;
					$check_if_exists->first()->save();
				}
			} else {
				Setting::where('name', 'variant_' . $product->id)->where('company_id', Auth::user()->company->id)->where('value_2', "attribute_2")->delete();

				//if no attribute_2, delete all attribute_2 data of all variants
				foreach($product->variants as $variant) {
					$variant->attribute_2 = null;
					$variant->save();
				}

			}

			if (!empty($request->attribute_3)) {
				$check_if_exists = Setting::where('name', 'variant_' . $product->id)->where('company_id', Auth::user()->company->id)->where('value_2', "attribute_3")->get();

				if ($check_if_exists->count() == 0) {
					$new_variant_column = new Setting;
					$new_variant_column->company_id = Auth::user()->company->id;
					$new_variant_column->name = "variant_" . $product->id;
					$new_variant_column->value = $request->attribute_3;

					$attr_no = $count_variants_added + 1;
					$new_variant_column->value_2 = strval("attribute_3");
					try {
						$new_variant_column->save();
						$count_variants_added = $count_variants_added + 1;
					} catch (Exception $e) {

					}
				} else {
					$check_if_exists->first()->value = $request->attribute_3;
					$check_if_exists->first()->save();
				}
			} else {
				Setting::where('name', 'variant_' . $product->id)->where('company_id', Auth::user()->company->id)->where('value_2', "attribute_3")->delete();
				foreach($product->variants as $variant) {
					$variant->attribute_3 = null;
					$variant->save();
				}
			}

			if (!empty($request->attribute_4)) {
				$check_if_exists = Setting::where('name', 'variant_' . $product->id)->where('company_id', Auth::user()->company->id)->where('value_2', "attribute_4")->get();

				if ($check_if_exists->count() == 0) {
					$new_variant_column = new Setting;
					$new_variant_column->company_id = Auth::user()->company->id;
					$new_variant_column->name = "variant_" . $product->id;
					$new_variant_column->value = $request->attribute_4;

					$attr_no = $count_variants_added + 1;
					$new_variant_column->value_2 = strval("attribute_4");
					try {
						$new_variant_column->save();
						$count_variants_added = $count_variants_added + 1;
					} catch (Exception $e) {

					}
				} else {
					$check_if_exists->first()->value = $request->attribute_4;
					$check_if_exists->first()->save();
				}
			} else {
				Setting::where('name', 'variant_' . $product->id)->where('company_id', Auth::user()->company->id)->where('value_2', "attribute_4")->delete();
				foreach($product->variants as $variant) {
					$variant->attribute_4 = null;
					$variant->save();
				}
			}

			if (!empty($request->attribute_5)) {
				$check_if_exists = Setting::where('name', 'variant_' . $product->id)->where('company_id', Auth::user()->company->id)->where('value_2', "attribute_5")->get();

				if ($check_if_exists->count() == 0) {
					$new_variant_column = new Setting;
					$new_variant_column->company_id = Auth::user()->company->id;
					$new_variant_column->name = "variant_" . $product->id;
					$new_variant_column->value = $request->attribute_5;

					$attr_no = $count_variants_added + 1;
					$new_variant_column->value_2 = strval("attribute_5");
					try {
						$new_variant_column->save();
						$count_variants_added = $count_variants_added + 1;
					} catch (Exception $e) {

					}
				} else {
					$check_if_exists->first()->value = $request->attribute_5;
					$check_if_exists->first()->save();
				}
			} else {
				Setting::where('name', 'variant_' . $product->id)->where('company_id', Auth::user()->company->id)->where('value_2', "attribute_5")->delete();
				foreach($product->variants as $variant) {
					$variant->attribute_5 = null;
					$variant->save();
				}
			}
			



			$success = "Variant columns modified!";

			return redirect('/'.Auth::user()->url().'/products/'.$product->slug)->with(compact('sidebar', 'product', 'title', 'success'));
		} catch (Exception $e) {

			$error = $e->getMessage();

			return redirect('/'.Auth::user()->url().'/products/'.$product->slug)->with(compact('error', 'product', 'title', 'confirmation_message'));
		}
	}

	public function addVariant($slug, $product_slug, Request $request){
		try {
			$company = Auth::user()->company;
			$product = Product::where('slug', $product_slug)->firstOrFail();
			
			Builder::macro('if', function ($condition, $column, $operator, $value) {
				if ($condition) {
					return $this->where($column, $operator, $value);
				}
				return $this;
			});

			//conditional search http://themsaid.com/laravel-query-conditions-20160425/
			$variant_exists = Variant::where('company_id', $company->id)
				->where('product_id', $product->id)
				->if($request->attribute_1, 'attribute_1', '=', $request->attribute_1)
				->if($request->attribute_2, 'attribute_2', '=', $request->attribute_2)
				->if($request->attribute_3, 'attribute_3', '=', $request->attribute_3)
				->if($request->attribute_4, 'attribute_4', '=', $request->attribute_4)
				->if($request->attribute_5, 'attribute_5', '=', $request->attribute_5)
				->get()->count();

	        if($variant_exists == 0) {
				$variant = new Variant;

				$variant->inventory = $request->inventory;
				$variant->price = $request->price;
				$variant->company_id = $company->id;

				if (isset($request->attribute_1)) {
					$variant->attribute_1 = $request->attribute_1;
				}
				if (isset($request->attribute_2)) {
					$variant->attribute_2 = $request->attribute_2;
				}
				if (isset($request->attribute_3)) {
					$variant->attribute_3 = $request->attribute_3;
				}
				if (isset($request->attribute_4)) {
					$variant->attribute_4 = $request->attribute_4;
				}
				if (isset($request->attribute_5)) {
					$variant->attribute_5 = $request->attribute_5;
				}

				$product->variants()->save($variant);
				
				return response()->json([
					'success' => 1,
				    'message' => "Variant added!",
				    'variant' => $variant,
				    'total_inventory' => $product->total_inventory
				]);
			} else {
				return response()->json([
					'success' => 0,
				    'message' => "Variant already exists!",
				    'total_inventory' => $product->total_inventory
				]);
			}

		} catch (Exception $e) {
			return response()->json([
			    'success' => 0,
			    'message' => $e->getMessage()
			]);
		}
	}

	public function editVariant($slug, $product_slug, Request $request){
		try {
			$validatedData = $request->validate([
		        'attribute_1' => 'required',
		        'price' => 'numeric|min:0',
		        'quantity' => 'nullable|numeric|min:0',
		    ]);

			$sidebar = "Products";
			$product = Product::where('slug', $product_slug)->firstOrFail();
	    	$title = $product->name;
			$variant = Variant::findOrFail($request->variant_id);
			$variant->update($request->all());

			return back()->with(['success' => "Variant edited!", 'sidebar' => $sidebar, 'title' => $title, 'product' => $product]);
		} catch (Exception $e) {
			$sidebar = "Products";
	    	$title = Product::where('slug', $product_slug)->firstOrFail()->name;

			return back()->with(['error' => $e->getMessage(), 'sidebar' => $sidebar, 'title' => $title]);
		}
	}

	public function deleteVariant($slug, $product_slug, $variant_id, Request $request){
		try {
			$variant = Variant::findOrFail($variant_id);
			$variant->delete();

			$product = Product::with('variants')->where('slug', $product_slug)->firstOrFail();

			$sidebar = "Products";
			$title = $product->name;
			$success = "Variant deleted!";

			return back()->with(compact('sidebar', 'product', 'title', 'success'));

		} catch (Exception $e) {

			$product = Product::with('variants')->where('slug', $product_slug)->firstOrFail();

			$sidebar = "Products";
			$title = $product->name;
			$error = $e->getMessage();

			return back()->with(compact('sidebar', 'product', 'title', 'error'));
		}
	}

	public function toggleAvailability($product_id){
		try {
			$product = Product::findOrFail($product_id);

			if ($product->is_available){
				$product->is_available = false;
				$product->save();

				return response()->json([
					'name' => $product->name,
					'is_available' => $product->is_available
				]);
			} else {
				$product->is_available = true;
				$product->save();

				return response()->json([
				    'name' => $product->name,
					'is_available' => $product->is_available
				]);
			}
		} catch (Exception $e) {
			return response()->json([
				    'success' => 0,
				    'message' => $e->getMessage()
				]);
		}
	}

	public function toggleAvailabilityVariant($variant_id){
		try {
			$variant = Variant::findOrFail($variant_id);

			if ($variant->is_available){
				$variant->is_available = false;
				$variant->save();

				return response()->json([
					'class' => "variant_" . $variant->id,
					'is_available' => $variant->is_available
				]);
			} else {
				$variant->is_available = true;
				$variant->save();

				return response()->json([
				    'class' => "variant_" . $variant->id,
					'is_available' => $variant->is_available
				]);
			}
		} catch (Exception $e) {
			return response()->json([
				    'success' => 0,
				    'message' => $e->getMessage()
				]);
		}
	}
}