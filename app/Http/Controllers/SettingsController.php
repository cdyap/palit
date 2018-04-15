<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Shipping;
use App\AppSettings;
use App\PaymentMethod;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $sidebar = "Settings";
        $title = "Settings";
        $company = Auth::user()->company;

        return view('admin.settings',compact('sidebar', 'title', 'company'));
    }

    public function company(){
        $sidebar = "Settings";
        $title = "Company";
        $company = Auth::user()->company;

        return view('admin.settings_company',compact('sidebar', 'title', 'company'));
    }

    public function company_edit(){
        $sidebar = "Settings";
        $title = "Edit company";
        $company = Auth::user()->company;

        return view('admin.settings_company_edit',compact('sidebar', 'title', 'company'));
    }

    public function company_update(Request $request){
        try {
            $company = Auth::user()->company;

            $validatedData = $request->validate([
                'name' => 'required|unique:companies,name,'.$company->id
            ]);

            $company->update($request->all());

            return redirect('/settings/company')->with(['success' => "Company information updated!"]);

        } catch (Exception $e) {
            return redirect('/settings/company');
        }        
    }

    public function shipping(){
        $sidebar = "Settings";
        $title = "Shipping";
        $company = Auth::user()->company;
        $shippings = Shipping::orderBy('id')->get();

        $currencies = AppSettings::where('name', 'currency')->get();

        return view('admin.settings_shipping',compact('sidebar', 'title', 'company', 'shippings', 'currencies'));
    }

    public function shipping_create(Request $request){
        $shipping = new Shipping;
        $company = Auth::user()->company;
        
        $validatedData = $request->validate([
            'name' => 'required',
            'currency' => 'required',
            'price' => 'numeric|min:0',
        ]);

        $shipping->name = $request->name;
        if (!empty($request->description)) {
            $shipping->description = $request->description;
        }
        $shipping->price = $request->price;
        $shipping->currency = $request->currency;

        $company->shippings()->save($shipping);

        return back()->with(['success' => " added!", 'emphasize' => $shipping->name]);
    }

    public function payment_method(){
        $sidebar = "Settings";
        $title = "Payment methods";
        $company = Auth::user()->company;
        $payment_methods = PaymentMethod::where('company_id',$company->id)->orderBy('name')->get();

        return view('admin.settings_payment_methods',compact('sidebar', 'title', 'company', 'payment_methods'));
    }

    public function toggleAvailabilityShipping($shipping_id){
        try {
            $shipping = Shipping::findOrFail($shipping_id);

            if ($shipping->is_available){
                $shipping->is_available = false;
                $shipping->save();

                return response()->json([
                    'class' => "shipping_" . $shipping->id,
                    'name' => $shipping->name,
                    'is_available' => $shipping->is_available
                ]);
            } else {
                $shipping->is_available = true;
                $shipping->save();

                return response()->json([
                    'class' => "shipping_" . $shipping->id,
                    'name' => $shipping->name,
                    'is_available' => $shipping->is_available
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                    'success' => 0,
                    'message' => $e->getMessage()
                ]);
        }
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
}
