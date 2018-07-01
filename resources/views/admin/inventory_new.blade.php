@extends('layouts.admin')

@section('content')
	<br>
	<h5><a href="/inventory">< Inventory</a></h5>
	<br>
	<div class="alerts-holder">
	@if(session('success'))
		<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>
			<strong>{{session('emphasize')}}</strong> {{session('success')}}
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
	@endif
	@if ($errors->any())
	    <div class="alert alert-error fade show z-depth-1-half" role="alert">
            @foreach ($errors->all() as $error)
                {{ $error }} <br>
            @endforeach
	    </div>
	@endif
	</div>
	<div class="row">
		<div class="col-lg-8 col-xs-12">
			<h4 id="AddItemToCart">1. Add products and variants to delivery cart</h4>
			<p class="caption">SELECT PRODUCT TO ADD</p>
			<form action="/inventory/getProduct" method="GET" class="select-product">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="form-group">
					<select name="product_id" class="form-control select-product" required>
						<option selected="selected" disabled>None</option>
						@foreach($products as $product)
							<option value="{{$product->id}}">{{$product->name}}</option>
						@endforeach						
					</select>
				</div>
				<button type="submit" class="hide">Submit</button>
			</form>
			<form action="/products/store" method="POST" class="">
				<div class="block hide product-block" style="margin-top:0;padding-bottom:30px;">
					<h4 class="product-name" style="margin-bottom:0">Products</h4>
					<div class="row">
						<div class="col">
							<p class="caption">INVENTORY:</p>
							<h5 class="product-inventory"></h5>
						</div>
						<div class="col">
							<p class="caption">INCOMING:</p>
							<h5 class="product-incoming"></h5>
						</div>
						<div class="col">
							<p class="caption">ORDERS:</p>
							<h5 class="product-orders"></h5>
						</div>
					</div>
					<br>
					<div class="row product-to-add">
						<div class="col-xs-6 col-sm-4">
							<div class="form-group">
								<label for="quantity">Quantity to add:</label>
								<input type="number" class="form-control product-quantity" name="xx" min="0" data-product="">
							</div>
						</div>
					</div>
					<div class="row variants-to-add">
						<div class="col">
							<table class="table">
								<thead>
									<th></th>
								</thead>
								<tbody>
									<td></td>
								</tbody>
							</table>
						</div>
					</div>
					<br>
					<a href="#finalize-delivery" class="button add-to-delivery">Add to delivery cart</a>
				</div>
				
			</form>
			<br><br>
		</div>
		<div class="col-lg-8 col-xs-12 hide delivery-cart">
			<h4 id="finalize-delivery">2. Finalize delivery</h4>
			<form action="/inventory/store" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="block">
					<h4>Delivery details</h4>
					<br>
					<br>
					<div class="form-row">
						<div class="col-xs-8 col-sm-6 form-group">
							<label for="date">Expected date of arrival:*</label>
							<input type="date" name="expected_arrival" id="expected_arrival" class="form-control datepicker {{ $errors->has('expected_arrival') ? 'has-error' : ''}}" required value="{{ old('expected_arrival') }}" style="background: white!important">
						</div>
					</div>
					<div class="form-row">
						<div class="col-xs-10 col-sm-9 form-group">
							<label for="date">Supplier:*</label>
							<input type="text" name="supplier" id="supplier" class="form-control {{ $errors->has('supplier') ? 'has-error' : ''}}"  value="{{ old('supplier') }}" required>
						</div>
					</div>
					<p class="note" style="margin-bottom:0;margin-top:10px;">* Required field</p>	
					<br>
					<br>
					<label>Delivery cart:</label>
					<table class="table">
						<thead>
							<th>Product</th>
							<th>Variant</th>
							<th>Quantity</th>
							<th style="max-width: 50px;"></th>
						</thead>
						<tbody class="delivery-cart">

						</tbody>
					</table>
					<br>
					{{-- <a href="#AddItemToCart" class="button ghost">Add more items</a> --}}
					<button type="submit" class="button">Add delivery</button>	
				</div>
			</form>
		</div>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js">
@endsection

@section('custom_js')	
	<script type="text/javascript">
	// $(window).bind('beforeunload', function(){
	// 	return true;
	// });
	</script>
@endsection