@extends('layouts.admin')

@section('content')
	<br>
	@if ($errors->any())
		<div class="alerts-holder">
		    <div class="alert alert-error fade show alert-dismissible z-depth-1-half" role="alert">
	            @foreach ($errors->all() as $error)
	                {{ $error }} <br>
	            @endforeach
	            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
		    </div>
		</div>
	@endif
	<h5><a href="/products/{{$product->slug}}">< {{$product->name}}</a></h5>
	<div class="row">
		<div class="col-lg-8">
			<form action="/products/{{$product->slug}}/update" method="POST" class="with-cascading-disabling">
				<div class="block">
					<h4>Product information for <b>{{$product->name}}</b>:</h4>
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="_method" value="PATCH">
					<div class="form-group">
						<label for="name">Name:*</label>
						<input type="text" name="name" class="form-control {{ $errors->has('name') ? 'has-error' : ''}}" required value="{{ $product->name }}">
					</div>
					<div class="form-group">
						<label for="description">Description:*</label>
						<textarea name="description" required class="form-control {{ $errors->has('description') ? 'has-error' : ''}}" rows="4">{{ $product->description }}</textarea>
					</div>
					<div class="form-group">
						<label for="description">SKU:</label>
						<input type="text" name="SKU" class="form-control {{ $errors->has('SKU') ? 'has-error' : ''}}" value="{{ $product->SKU }}">
					</div>
					<p class="note" style="margin-bottom:0;margin-top:10px;">* Required field</p>			
				</div>
				<div class="block">
					<h4>Pricing:*</h4>
					<div class="form-row">
						@if($product->hasSameVariantPrices())
							<div class="input-group">
								<div class="input-group-prepend">
						        	<div class="input-group-text">{{$company->currency_name}} ({{$company->currency}})</div>
						        </div>
								<input type="number" name="price" class="form-control {{ $errors->has('price') ? 'has-error' : ''}}"  min="0", value="{{ $product->price }}" required>
							</div>
							<div class="col-lg-12">
								<p class="note" style="margin-bottom:0;margin-top:10px;">Prices of all this product's variants will also be updated.</p>
							</div>
						@else
							<p class="note" style="margin-bottom:0;margin-top:10px;">Note: You may not edit this product's price here since its variants have varying prices.</p>
						@endif
						<div class="col-lg-12">
							<p class="note" style="margin-bottom:0;margin-top:10px;">* Required field</p>	
						</div>
					</div>
				</div>
				<div class="block">
					<h4>Shipping and selling:</h4>
					<div class="form-group">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="is_shipped" name="is_shipped" value="1" {{ ($product->is_shipped) ? "checked" : "" }}>
							<label class="form-check-label" for="is_shipped">
							Require shipping?</label>
						</div>

						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="overselling_allowed" name="overselling_allowed" value="1" {{ ($product->overselling_allowed) ? "checked" : "" }}>
							<label class="form-check-label" for="overselling_allowed">
							Allow orders to exceed current inventory?</label>
						</div>
						
					</div>	
					<div class="form-group">
						<label for="price">Items per shipment:</label>
						<input type="number" name="item_per_shipment" class="form-control {{ $errors->has('item_per_shipment') ? 'has-error' : ''}}"  min="0", value="{{ $product->item_per_shipment }}" style="width:50%">
						<p class="note" style="margin-bottom:0;margin-top:10px;">How many items can fit in one shipment?</p>
					</div>
				</div>
				@if($product->variants->count() == 0)
					<div class="block">
						<h4>Inventory:</h4>
						<p class="note">If this product will have variants, you may leave this field blank.<br>The quantity will be the sum of the quantities of its variants.</p>
						<div class="form-row">
							<div class="form-group col-sm-6">
								<label for="quantity">Initial quantity:</label>
								<input type="number" name="quantity" class="form-control {{ $errors->has('quantity') ? 'has-error' : ''}}"  min="0", value="{{ $product->quantity }}">
							</div>
						</div>
					</div>
				@endif
				<br>
				<button type="submit" class="button z-depth-1">Save product</button>
			</form>
		</div>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js">
@endsection