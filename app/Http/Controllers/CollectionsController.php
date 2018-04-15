<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Collection;
use \App\Product;
use Auth;
use Log;
use DB;
use File;
use Carbon\Carbon;

class CollectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $sidebar = "Collections";
        $title = "All collections";
        $collections = Collection::with('products')->get();

        $available_collections = $collections->where('is_available', true);
        $unavailable_collections = $collections->where('is_available', false);

        return view('admin.collections',compact('sidebar', 'title', 'collections', 'available_collections', 'unavailable_collections'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $sidebar = "Collections";
        $title = "All collections";

        return view('admin.collections_new',compact('sidebar', 'title'));
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
            'name' => 'required|unique:collections',
            'description' => 'required',
        ]);

        $collection = new Collection;

        $collection->name = $request->name;
        $collection->description = $request->description;
        $collection->is_available = true;

        $company = Auth::user()->company;
        $company->collections()->save($collection);

        return redirect('/collections')->with(['success' => " added!", 'emphasize' => $collection->name]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($collection_slug)
    {
        //
        try {
            $sidebar = "Collections";
            
            $collection = Collection::with('products')->where('slug', $collection_slug)->firstOrFail();    
            $title = $collection->name;

            $collection_products = $collection->products->pluck('id');

            $products = DB::table('products')->whereNotIn('id', $collection_products)->get();


            return view('admin.collections_show',compact('sidebar', 'title', 'collection', 'products'));
        } catch (Exception $e) {
            return redirect('/collections');
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($collection_slug)
    {
        try {
            $collection = Collection::where('slug', $collection_slug)->firstOrFail();
            $sidebar = "Collections";
            $title = "Edit ". $collection->name;

            return view('admin.collections_edit',compact('sidebar', 'title', 'collection'));
        } catch (Exception $e) {
            return redirect('/collections');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($collection_slug, Request $request)
    {
        //
        try {
            $collection = Collection::where('slug', $collection_slug)->firstOrFail();
            $sidebar = "Collections";
            $title = "Edit " . $collection->name;

            $validatedData = $request->validate([
                'name' => 'required|unique:collections,name,'.$collection->id,
                'description' => 'required',
            ]);

            $collection->update($request->all());

            return redirect('/collections/'.$collection->slug)->with(['success' => " edited!", 'emphasize' => $collection->name]);

        } catch (Exception $e) {
            return redirect('/collections');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($collection_slug, Request $request)
    {
        //
        try {
            $collection = Collection::where('slug', $collection_slug)->firstOrFail();
            $collection->delete();

            return redirect('/collections')->with(['success' => " deleted!", 'emphasize' => $collection->name]);
        } catch (Exception $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
    }

    public function toggleAvailability($collection_id){
        try {
            $collection = Collection::findOrFail($collection_id);

            if ($collection->is_available){
                $collection->is_available = false;
                $collection->save();

                return response()->json([
                    'name' => $collection->name,
                    'is_available' => $collection->is_available
                ]);
            } else {
                $collection->is_available = true;
                $collection->save();

                return response()->json([
                    'name' => $collection->name,
                    'is_available' => $collection->is_available
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                    'success' => 0,
                    'message' => $e->getMessage()
                ]);
        }
    }

    public function addProducts($collection_slug, Request $request){
        try {
            $collection = Collection::with('products')->where('slug', $collection_slug)->firstOrFail();
            $sidebar = "Collections";
            $title = $collection->name;

            $current_products = DB::table('collection_product')->where('collection_id', $collection->id)->get()->pluck('product_id');

            $collection->products()->attach(array_values(array_diff($request->products, $current_products->toArray())));

            return redirect('/collections/'.$collection_slug)->with(['success' => "Products added!"]);

        } catch (Exception $e) {
            return redirect('/collections');
        }
    }

    public function removeFromCollection(Request $request){
        try {
            $inputs = json_decode($request->getContent(), true);

            $product = Product::where('slug', $inputs['product_slug'])->firstOrFail();
            $collection = Collection::where('slug', $inputs['collection_slug'])->firstOrFail();

            $collection->products()->detach($product->id);
            return response()->json([
                'success' => 1,
                'product_name' => $product->name,
                'collection_name' => $collection->name
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => 0,
                'error' => $e
            ]);
        }   
    }

    public function getProductsForCollection($collection_slug){

        try {


            $collection = Collection::with('products')->where('slug', $collection_slug)->firstOrFail();

            $collection_products = $collection->products->pluck('id');

            $products = DB::table('products')->whereNotIn('id', $collection_products)->orderBy('name')->get();

            $products_for_collection = $products->mapToGroups(function ($item, $key) {
                return [$item->name => $item->id];
            })->toArray();

            return response()->json([
                'success' => 1,
                'count' => $products->count(),
                'products' => $products_for_collection
            ]);

        } catch (Exception $e) {

            return response()->json([
                'success' => 0,
                'error' => $e
            ]);
        }   
    }
    public function uploadHeaderImage($collection_slug, Request $request){
        try {
            $product = Collection::where('slug', $collection_slug)->firstOrFail();
            // Creating a new time instance, we'll use it to name our file and declare the path
            $time = Carbon::now();
            // Requesting the file from the form

            $validateImage = $request->validate([
                'file' => 'image|required|dimensions:max_width=1000,max_height=1000|max:2048'
            ]);

            $image = $request->file('file');
            // Getting the extension of the file 
            $extension = $image->getClientOriginalExtension();
            // Creating the directory, for example, if the date = 18/10/2017, the directory will be 2017/10/
            $directory = date_format($time, 'Y') . '/' . date_format($time, 'm');
            // Creating the file name: random string followed by the day, random number and the hour
            $filename = str_random(5).date_format($time,'d').rand(1,9).date_format($time,'h').".".$extension;

            //delete existing image
            if(File::exists(public_path()."/uploads/".$product->image_url)) {
                File::delete(public_path()."/uploads/".$product->image_url);
            }
            // This is our upload main function, storing the image in the storage that named 'public'
            $upload_success = $image->storeAs($directory, $filename, 'public');
            // If the upload is successful, return the name of directory/filename of the upload.
            if ($upload_success) {
                $product->image_url = $upload_success;
                $product->save();
                return response()->json("/uploads/".$upload_success, 200);
            }
            // Else, return error 400
            else {
                return response()->json('error', 400);
            }

        } catch (Exception $e) {
            return response()->json('error', 400);
        }
        
    }

    public function deleteHeaderImage($collection_slug, Request $request){
        $sidebar = "Collections";
        
        try {
            $collection = Collection::where('slug', $collection_slug)->firstOrFail();
            $title = $collection->name;

            //delete existing image
            if(File::exists(public_path()."/uploads/".$collection->image_url)) {
                File::delete(public_path()."/uploads/".$collection->image_url);
                $collection->image_url = null;
                $collection->save();
                $success = "Header image deleted!";

                return back()->with(compact('sidebar', 'collection', 'title', 'success'));
            } else {
                $success = "No header image to delete!!";
                return back()->with(compact('sidebar', 'collection', 'title', 'success'));
            }

        } catch (Exception $e) {
            return response()->json('Product not found!', 400);
        }
    }
}
