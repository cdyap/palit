<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('/', 'HomeController@index');

Route::group(['middleware' => 'auth', 'web'], function () {
    Route::get('/dashboard', 'AdminController@index');
    
    //products
    Route::get('/products', 'ProductsController@index');
    
	Route::get('/products/new', 'ProductsController@new');
	Route::post('/products/save', 'ProductsController@save');
	Route::get('/products/{product_slug}', 'ProductsController@show');
	Route::delete('/products/{product_slug}/delete', 'ProductsController@delete');
	Route::get('/products/{product_slug}/edit', 'ProductsController@edit');
	Route::patch('/products/{product_slug}/update', 'ProductsController@update');
	Route::patch('/toggleAvailability/{product_id}', 'ProductsController@toggleAvailability');
	
	Route::post('/products/{product_slug}/addVariantColumn', 'ProductsController@addVariantColumn');

	Route::post('/products/{product_slug}/addVariant', 'ProductsController@addVariant');
	Route::delete('/products/{product_slug}/{variant_id}/delete', 'ProductsController@deleteVariant');
	Route::patch('/products/{product_slug}/editVariant', 'ProductsController@editVariant');
	Route::patch('/toggleAvailabilityVariant/{variant_id}', 'ProductsController@toggleAvailabilityVariant');
	Route::patch('/toggleAvailabilityCollection/{collection_id}', 'CollectionsController@toggleAvailability');

	Route::post('/products/{product_slug}/uploadHeaderImage' , 'ProductsController@uploadHeaderImage');
	Route::delete('/products/{product_slug}/deleteHeaderImage' , 'ProductsController@deleteHeaderImage');


	//collections
	Route::get('/collections', 'CollectionsController@index');
	Route::get('/collections/new', 'CollectionsController@create');
	Route::post('/collections/store', 'CollectionsController@store');
	Route::get('/collections/{collection_slug}', 'CollectionsController@show');
	Route::get('/collections/{collection_slug}/edit', 'CollectionsController@edit');
	Route::patch('/collections/{collection_slug}/update', 'CollectionsController@update');
	Route::post('/collections/{collection_slug}/addProducts', 'CollectionsController@addProducts');
	Route::delete('/collections/{collection_slug}/delete', 'CollectionsController@delete');

	Route::post('/collections/{collection_slug}/uploadHeaderImage' , 'CollectionsController@uploadHeaderImage');
	Route::delete('/collections/{collection_slug}/deleteHeaderImage' , 'CollectionsController@deleteHeaderImage');

	Route::post('/removeFromCollection', 'CollectionsController@removeFromCollection');
	Route::get('/getProductsForCollection/{collection_slug}', 'CollectionsController@getProductsForCollection');

	//orders
	Route::get('/orders', 'OrdersController@orders_unpaid');
	Route::get('/orders/paid', 'OrdersController@orders_paid');
	Route::get('/orders/shipped', 'OrdersController@orders_shipped');
	Route::get('/orders/cancelled', 'OrdersController@orders_cancelled');
	Route::post('/orders/{hash}/confirm', 'OrdersController@confirm');
	Route::post('/orders/{hash}/fulfill', 'OrdersController@fulfill');
	Route::delete('/orders/{hash}/delete', 'OrdersController@delete');

	//inventory
	Route::get('/inventory', 'InventoryController@index');
	Route::get('/inventory/new', 'InventoryController@create');
	Route::get('/inventory/getProduct', 'InventoryController@getProduct');
	Route::post('/inventory/store', 'InventoryController@store');
	Route::delete('/inventory/{id}/delete', 'InventoryController@destroy');
	
	Route::get('/inventory/delivery/{delivery_slug}', 'InventoryController@view_delivery');
	Route::post('/inventory/delivery/{delivery_id}/receive', 'InventoryController@receive_delivery');

	Route::post('/sort', '\Rutorika\Sortable\SortableController@sort');
	
	//settings
	Route::get('/settings', 'SettingsController@index');
	Route::get('/settings/company', 'SettingsController@company');
	Route::get('/settings/company/edit', 'SettingsController@company_edit');
	Route::patch('/settings/company/update', 'SettingsController@company_update');
	Route::get('/settings/shipping', 'SettingsController@shipping');
	Route::post('/settings/shipping/create', 'SettingsController@shipping_create');
	Route::get('/settings/payment_methods', 'SettingsController@payment_method');
	Route::patch('/toggleAvailabilityShipping/{shipping_id}', 'SettingsController@toggleAvailabilityShipping');
});

//Shopping page
Route::get('/{company_slug}', 'OrdersController@order_page');
Route::post('/{company_slug}/addToCart', 'OrdersController@addToCart');
Route::get('/{company_slug}/cart', 'OrdersController@cart');
Route::post('/{company_slug}/shipping', 'OrdersController@shipping');
//if shipping page is refreshed
Route::get('/{company_slug}/shipping', 'OrdersController@shipping');

Route::post('/{company_slug}/checkout', 'OrdersController@checkout');
Route::get('/{company_slug}/checkout', 'OrdersController@checkout');

Route::post('/{company_slug}/save-order', 'OrdersController@store');
Route::get('/{company_slug}/save-order', 'OrdersController@redirect_to_home');

Route::get('/check-order-status', 'OrdersController@check_order_status');
Route::post('/view-order', 'OrdersController@find_order');
Route::get('/view-order', function () {
    return redirect('/check-order-status');
});

//helper functions
Route::patch('/removeFromCart/{slug}/{rowId}', 'OrdersController@removeFromCart');
Route::patch('/changeQuantity/{slug}/{rowId}', 'OrdersController@changeQuantity');

