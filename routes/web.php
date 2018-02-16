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



Route::group(['middleware' => 'auth', 'web', 'check_company'], function () {
    Route::get('/{slug}', 'AdminController@index');
    Route::get('/{slug}/products', 'ProductsController@index');
    
	Route::get('/{slug}/products/new', 'ProductsController@new');
	Route::post('/{slug}/products/save', 'ProductsController@save');
	Route::get('/{slug}/products/{product_slug}', 'ProductsController@show');
	Route::delete('/{slug}/products/{product_slug}/delete', 'ProductsController@delete');
	Route::get('/{slug}/products/{product_slug}/edit', 'ProductsController@edit');
	Route::patch('/{slug}/products/{product_slug}/update', 'ProductsController@update');
	Route::patch('/toggleAvailability/{product_id}', 'ProductsController@toggleAvailability');
	
	Route::post('/{slug}/products/{product_slug}/addVariantColumn', 'ProductsController@addVariantColumn');

	Route::post('/{slug}/products/{product_slug}/addVariant', 'ProductsController@addVariant');
	Route::delete('/{slug}/products/{product_slug}/{variant_id}/delete', 'ProductsController@deleteVariant');
	Route::patch('/{slug}/products/{product_slug}/editVariant', 'ProductsController@editVariant');
	Route::patch('/toggleAvailabilityVariant/{variant_id}', 'ProductsController@toggleAvailabilityVariant');

	Route::get('/{slug}/collections', 'AdminController@collections');
	Route::get('/{slug}/orders', 'AdminController@orders');
	Route::get('/{slug}/inventory', 'AdminController@inventory');

	Route::post('/sort', '\Rutorika\Sortable\SortableController@sort');
});