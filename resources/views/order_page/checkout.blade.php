@extends('layouts.order_page')

@section('content')
	<div class="container checkout">
		<div class="alerts-holder">
		@if(session('success'))
			<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>
				<strong>{{session('emphasize')}}</strong> {{session('success')}}
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
		@endif
		@if(session('error'))
			<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>
				<strong>{{session('emphasize')}}</strong> {{session('error')}}
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
		@endif
		@if(count($errors) > 0)
			<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>
				You have entered invalid information
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
		@endif
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<form action="/{{$company->slug}}/order/store" method="POST" id="checkout_form">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="row">
						<h4 class="with-underline text-bold">CUSTOMER INFORMATION</h4>
					</div>
					<div class="form-row">
						<div class="form-group col-xs-12">
							<label for="email">Email:*</label>
							<input type="text" name="email" class="form-control {{ $errors->has('email') ? 'has-error' : ''}}"  value="{{ old('email') }}">
						</div>
						@if($errors->has('email'))
							<p class="error-note">Please enter a valid email</p>
						@endif
					</div>
					<div class="form-row">
						<div class="form-group col-xs-12 col-md-6">
							<label for="first_name">First name:*</label>
							<input type="text" name="first_name" class="form-control {{ $errors->has('first_name') ? 'has-error' : ''}}"  value="{{ old('first_name') }}">
						</div>
						<div class="form-group col-xs-12 col-md-6">
							<label for="last_name">Last name:*</label>
							<input type="text" name="last_name" class="form-control {{ $errors->has('last_name') ? 'has-error' : ''}}"  value="{{ old('last_name') }}">
						</div>
						@if($errors->has('first_name') || $errors->has('last_name'))
							<p class="error-note">Please enter your first and last name</p>
						@endif
					</div>
					<div class="form-row">
						<div class="form-group col-xs-5 col-sm-5">
							<label for="country_code">Country code:*</label>
							<!-- country codes (ISO 3166) and Dial codes. -->
							<select name="country_code" class="form-control">
								<option value="+63" {{(old('country_code') == "+63" ) ? "selected" : "" }}>Philippines (+63)</option>
								<optgroup label="Other countries">
									@foreach($country_codes->whereNotIn('value', ['+63']) as $code)
										<option value="{{$code->value}}" {{(old('country_code') == $code->value ) ? "selected" : "" }} >{{$code->value_2}}</option>
									@endforeach
								</optgroup>
							</select>
						</div>
						<div class="form-group col-xs-7 col-sm-7">
							<label for="contact_number">Contact number:*</label>
							<input type="number" name="contact_number" class="form-control {{ $errors->has('contact_number') ? 'has-error' : ''}}"  min="0" value="{{ old('contact_number') }}">
						</div>
						@if($errors->has('country_code') || $errors->has('contact_number'))
							<p class="error-note">Please enter a valid phone number (e.g. +63 917 123 4567)</p>
						@endif
					</div>
					<div class="row">
						<h4 class="with-underline text-bold" style="margin-top:40px;">SHIPPING INFORMATION</h4>
					</div>
					<div class="form-row">
						<div class="form-group col-xs-12">
							<label for="shipping_address_1">Address line 1:*</label>
							<input type="text" name="shipping_address_1" class="form-control {{ $errors->has('shipping_address_1') ? 'has-error' : ''}}"  value="{{ old('shipping_address_1') }}">
						</div>
						@if($errors->has('shipping_address_1'))
							<p class="error-note">Please enter your shipping address</p>
						@endif
					</div>
					<div class="form-row">
						<div class="form-group col-xs-12">
							<label for="shipping_address_2">Address line 2:</label>
							<input type="text" name="shipping_address_2" class="form-control {{ $errors->has('shipping_address_2') ? 'has-error' : ''}}" value="{{ old('shipping_address_2') }}">
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-xs-8 col-sm-9">
							<label for="city">City:*</label>
							<input type="text" name="city" class="form-control {{ $errors->has('city') ? 'has-error' : ''}}"  value="{{ old('city') }}">
						</div>
						<div class="form-group col-xs-4 col-sm-3">
							<label for="city">Zip code:*</label>
							<input type="number" name="zip_code" class="form-control {{ $errors->has('zip_code') ? 'has-error' : ''}}"  value="{{ old('zip_code') }}">
						</div>
						@if($errors->has('city') || $errors->has('zip_code'))
							<p class="error-note">Please enter your city and zip code for delivery</p>
						@endif
					</div>
					<div class="form-row">
						<div class="form-group col-xs-12 col-sm-12">
							<label for="country">Country:*</label>
							<select name="country" class="form-control">	
								@foreach($countries as $country)
									<option value="{{$country->value}}" {{(old('country') == $country->value ) ? "selected" : "" }}>{{$country->value}}</option>
								@endforeach
							</select>
						</div>
						@if($errors->has('country'))
							<p class="error-note">Please enter your country for delivery</p>
						@endif
					</div>
					<br>
					<div class="row">
						<h4 class="with-underline text-bold" style="margin-top:40px;margin-bottom:-10px;">PAYMENT METHOD</h4>
					</div>
					<div class="row payment_method">
						@if($errors->has('payment_method'))
							<p class="error-note" style="">Please select a payment method</p>
						@endif
						<div class="col-xs-12 col-md-12" style="padding-left: 0;">
							@foreach($payment_methods->where('is_available', true) as $payment_method)
								<label class="payment-method">
	                                <input type="radio" name="payment_method" value="{{$payment_method->name}}" class="payment-method-radio" {{ (old('payment_method') == $payment_method->name) ? "checked " : "" }}>
									<div class="block">							
										<h4>{{$payment_method->name}}</h4>
										<h5 class="text-grey">{{$payment_method->description}}</h5>
									</div>
								</label>
							@endforeach
						</div>
					</div>
					<br>
					<div class="form-row">
						<div class="form-group col-xs-12">
							<a href="/{{$company->slug}}/shipping" class="button ghost">< Select shipping method</a>
							<button type="submit">Pay ></button>	
						</div>
					</div>
					
				</form>
			</div>
			<div class="col-xs-12 col-md-6">
                <h4 class="with-underline text-bold">CART</h4>    
                <table class="table">
                    <thead>
                        <th>Product</th>
                        <th class="text-right" style="width:100px;">Quantity</th>
                        <th class="text-right">Total</th>
                    </thead>
                    <tbody>
                        @foreach($cart->where('name', 'Product') as $item)
                            <tr>
                                <td class="align-middle">{{$item->id->name}}<br><span class="text-grey">{{$item->options->description}}</span></td>
                                <td class="text-right align-middle">{{$item->qty}}</td>
                                <td class="text-right align-middle">{{$item->options->currency . " " . number_format($item->subtotal, 2, '.', ',')}}</td>
                            </tr>
                        @endforeach
                        <tr class="">
                            <td colspan="2"><span class="note">SHIPPING:<br></span>{{$shipping_mode->name}}<br><span class="text-grey">{{$shipping_mode->description}}</span></td>
                            <td class="text-right align-middle">{{$shipping_mode->total_cost}}</td>
                        </tr>
                        <tr class="text-bold">
                            <td colspan="2">Total amount due:</td>
                            <td class="text-right align-middle">{{$company->currency}} {{$cart_total}}</td>
                        </tr>
                    </tbody>
                </table>
			</div>
		</div>
	</div>
@endsection