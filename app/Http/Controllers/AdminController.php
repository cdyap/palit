<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminController extends Controller
{
    //

    public function __construct(){
        $this->middleware('check_session');
	}

    public function index(){
    	$sidebar = "Dashboard";
        $company = Auth::user()->company;
        $all_orders = $company->orders;
        $pending_orders = $company->orders->where('date_fulfilled', null);
        $now = new Carbon;

        $lead_time_payment = DB::table('orders')
            ->select(DB::raw('AVG(now() - date_paid) AS avg'))
            ->where('company_id', $company->id)
            ->where('date_fulfilled', null)
            ->get();

        $unpaid_orders_count = DB::table('orders')
            ->select(DB::raw('count(id)'))
            ->where('company_id', $company->id)
            ->where('date_paid', null)
            ->where('date_fulfilled', null)
            ->get();

        $unpaid_orders_total = DB::table('order_items')
            ->join ('orders', 'orders.id', '=' , 'order_items.order_id')
            ->select(DB::raw('sum(order_items.quantity*order_items.price) + sum(orders.shipping_price) as aggregate'))
            ->where('orders.company_id', $company->id)
            ->where('orders.date_paid', null)
            ->where('orders.date_fulfilled', null)
            ->where('orders.deleted_at', null)
            ->where('order_items.deleted_at', null)
            ->get();

        $unfulfilled_orders_count = DB::table('orders')
            ->select(DB::raw('count(id)'))
            ->where('company_id', $company->id)
            ->where('orders.date_paid', "<>", null)
            ->where('date_fulfilled', null)
            ->get();

        $unfulfilled_orders_total = DB::table('order_items')
            ->join ('orders', 'orders.id', '=' , 'order_items.order_id')
            ->select(DB::raw('sum(order_items.quantity*order_items.price) + sum(orders.shipping_price) as aggregate'))
            ->where('orders.company_id', $company->id)
            ->where('orders.date_paid', "<>", null)
            ->where('orders.date_fulfilled', null)
            ->where('orders.deleted_at', null)
            ->where('order_items.deleted_at', null)
            ->get();

    	return view('admin.index',compact('sidebar', 'company', 'all_orders', 'pending_orders', 'lead_time_payment', 'unpaid_orders_count', 'unpaid_orders_total', 'unfulfilled_orders_total', 'unfulfilled_orders_count'));
    }

    public function products(){
    	$sidebar = "Products";
    	return view('admin.products',compact('sidebar'));
    }

    public function collections(){
    	$sidebar = "Collections";
    	return view('admin.collections',compact('sidebar'));
    }

    public function orders(){
    	$sidebar = "Orders";
    	return view('admin.orders',compact('sidebar'));
    }

    public function inventory(){
    	$sidebar = "Inventory";
    	return view('admin.inventory',compact('sidebar'));
    }
}
