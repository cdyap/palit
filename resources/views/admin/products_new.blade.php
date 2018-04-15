@extends('layouts.admin')

@section('content')
	<br>
	<h5><a href="/products">< All products</a></h5>
	<div class="row">
		<div class="col-lg-8">
			<form action="/products/save" method="POST" class="with-cascading-disabling">
				<div class="block">
					<h4>Product information:</h4>
					@if ($errors->any())
					    <div class="alert alert-error fade show z-depth-1-half" role="alert">
				            @foreach ($errors->all() as $error)
				                {{ $error }} <br>
				            @endforeach
					    </div>
					@endif
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="form-group">
						<label for="name">Name:*</label>
						<input type="text" name="name" class="form-control {{ $errors->has('name') ? 'has-error' : ''}}" required value="{{ old('name') }}">
					</div>
					<div class="form-group">
						<label for="description">Description:*</label>
						<textarea name="description" required class="form-control {{ $errors->has('description') ? 'has-error' : ''}}" rows="4">{{ old('description') }}</textarea>
					</div>
					<div class="form-group">
						<label for="description">SKU:</label>
						<input type="text" name="SKU" class="form-control {{ $errors->has('SKU') ? 'has-error' : ''}}" value="{{ old('SKU') }}">
					</div>
					<p class="note" style="margin-bottom:0;margin-top:10px;">* Required field</p>			
				</div>
				<div class="block">
					<h4>Pricing:</h4>
					<div class="form-row">
						<div class="form-group col-sm-6">
							<label for="currency">Currency:*</label>
							<select name="currency" class="form-control {{ $errors->has('currency') ? 'has-error' : ''}}" required>
								<option selected="selected" disabled>None</option>
								@foreach($currencies as $currency)
									<option value="{{$currency->value_2}}">{{$currency->value}}</option>
								@endforeach						
							</select>
						</div>
						<div class="form-group col-sm-6">
							<label for="price">Price:*</label>
							<input type="number" name="price" class="form-control {{ $errors->has('price') ? 'has-error' : ''}}"  min="0", value="{{ old('price') }}" required>
						</div>
						<p class="note" style="margin-bottom:0;margin-top:10px;">* Required field</p>
					</div>
				</div>
				<div class="block">
					<h4>Shipping and selling:</h4>
					<div class="form-group">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="is_shipped" name="is_shipped" value="1" {{ (!empty(old('is_shipped'))) ? "checked" : "" }} >
							<label class="form-check-label" for="is_shipped">
							Require shipping?</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="overselling_allowed" name="overselling_allowed" value="1" {{ (!empty(old('overselling_allowed'))) ? "checked" : "" }} >
							<label class="form-check-label" for="overselling_allowed">
							Allow orders to exceed current inventory?</label>
						</div>
					</div>	
					<div class="form-group">
						<label for="price">Items per shipment:</label>
						<input type="number" name="item_per_shipment" class="form-control {{ $errors->has('item_per_shipment') ? 'has-error' : ''}}"  min="0", value="{{ (!empty(old('is_shipped'))) ? old('item_per_shipment') : 1  }}" style="width:50%">
						<p class="note" style="margin-bottom:0;margin-top:10px;">How many items can fit in one shipment?</p>
					</div>
				</div>

				<div class="block">
					<h4>Variants:</h4>
					<p class="note">Enter new columns consecutively. Clearing a column will also clear all columns to the right.</p>
					<div class="form-row">
						<div class="col-md">
							<label for="attribute_1">Column 1:</label>
							<input type="text" name="attribute_1" class="form-control {{ $errors->has('attribute_1') ? 'has-error' : ''}}" value="{{ old('attribute_1') }}">
						</div>
						<div class="col-md">
							<label for="attribute_2">Column 2:</label>
							<input type="text" name="attribute_2" class="form-control {{ $errors->has('attribute_2') ? 'has-error' : ''}}" value="{{ old('attribute_2') }}" {{(empty(old('attribute_2')) ? "disabled" : "")}}>
						</div>
						<div class="col-md">
							<label for="attribute_3">Column 3:</label>
							<input type="text" name="attribute_3" class="form-control {{ $errors->has('attribute_3') ? 'has-error' : ''}}" value="{{ old('attribute_3') }}" {{(empty(old('attribute_3')) ? "disabled" : "")}}>
						</div>
						<div class="col-md">
							<label for="attribute_4">Column 4:</label>
							<input type="text" name="attribute_4" class="form-control {{ $errors->has('attribute_4') ? 'has-error' : ''}}" value="{{ old('attribute_4') }}" {{(empty(old('attribute_4')) ? "disabled" : "")}}>
						</div>
						<div class="col-md">
							<label for="attribute_5">Column 5:</label>
							<input type="text" name="attribute_5" class="form-control {{ $errors->has('attribute_5') ? 'has-error' : ''}}" value="{{ old('attribute_5') }}" {{(empty(old('attribute_5')) ? "disabled" : "")}}>
						</div>
					</div>
				</div>
				<div class="block">
					<h4>Inventory:</h4>
					<p class="note">If this product will have variants, you may leave this field blank.<br>The quantity will be the sum of the quantities of its variants.</p>
					<div class="form-row">
						<div class="form-group col-sm-6">
							<label for="quantity">Initial quantity:</label>
							<input type="number" name="quantity" class="form-control {{ $errors->has('quantity') ? 'has-error' : ''}}"  min="0" value="{{ old('quantity') }}">
						</div>
					</div>
				</div>
				<br>
				<button type="submit" class="button z-depth-1">Save product</button>
			</form>
		</div>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js">
@endsection