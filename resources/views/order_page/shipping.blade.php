@extends('layouts.order_page')

@section('content')
	<div class="container shipping">
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<h4 class="with-underline text-bold">SELECT A SHIPPING MODE:</h4>
				<form action="/{{$company->slug}}/checkout" method="POST">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					@foreach($shippings->where('is_available', true) as $shipping)
						<label class="shipping-method">
                            @if(!empty($selected_shipping_mode))
    							<input type="radio" name="shipping" value="{{$shipping->id}}" class="shipping-method-radio" required {{($selected_shipping_mode->id->id == $shipping->id) ? "checked" : ""}}>
                            @else
                                <input type="radio" name="shipping" value="{{$shipping->id}}" class="shipping-method-radio" required>
                            @endif
							<div class="block">							
								<h4>{{$shipping->name}}</h4>
								<h4 class="float-right text-bold no-underline">{{$shipping->total_cost}}</h4>
								<h5 class="text-grey">{{$shipping->description}}</h5>
							</div>
						</label>
					@endforeach
                    <a class="button ghost" href="/{{$company->slug}}" >< Add more to cart</a>
                    <button type="submit" class="checkout">Checkout ></button>
				</form>
				
			</div>
			<div class="col-xs-12 col-md-6">
                <h4 class="with-underline text-bold">CART</h4>    
                <table class="table shipping">
                    <thead>
                        <th>Item</th>
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
                        @foreach($shippings->where('is_available', true) as $shipping)
                            <tr class="shipping-mode shipping{{$shipping->id}}">
                                <td colspan="2"><span class="note">SHIPPING:<br></span>{{$shipping->name}}<br><span class="text-grey">{{$shipping->description}}</span></td>
                                <td class="text-right align-middle">{{$shipping->total_cost}}</td>
                            </tr>

                            @if(!empty($selected_shipping_mode))
                                @if($selected_shipping_mode->id->id == $shipping->id)
                                    <tr class="shipping-mode shipping{{$shipping->id}}">
                                        <td colspan="2" class="text-right text-bold">Total amount due:<br></td>
                                        <td class="text-right align-middle text-bold">{{$shipping->cart_cost}}</td>
                                    </tr>
                                @else
                                    <tr class="shipping-mode shipping{{$shipping->id}}">
                                        <td colspan="2" class="text-right text-bold">Total amount due:<br></td>
                                        <td class="text-right align-middle text-bold">{{$shipping->cart_cost}}</td>
                                    </tr>
                                @endif
                            @else
                                <tr class="shipping-mode shipping{{$shipping->id}}">
                                    <td colspan="2" class="text-right text-bold">Total amount due:<br></td>
                                    <td class="text-right align-middle text-bold">{{$shipping->cart_cost}}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
			</div>
		</div>
	</div>
@endsection