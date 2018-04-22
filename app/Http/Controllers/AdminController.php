<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //

    public function __construct(){
        $this->middleware('check_session');
	}

    public function index(){
    	$sidebar = "Dashboard";
        $company = Auth::user()->company;
    	return view('admin.index',compact('sidebar', 'company'));
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
