@extends('layouts.order_page')

@section('content')
	<div class="container view_order">
		<div class="row status">
			<div class="col text-center {{!empty($order->date_ordered) ? 'active' : '' }}">
				<i class="fas fa-shopping-basket"></i>
				<h6>Received</h6>
			</div>
			<div class="col text-center {{!empty($order->date_paid) ? 'active' : '' }}">
				<i class="fas fa-receipt"></i>
				<h6>Paid</h6>
			</div>
			<div class="col text-center {{!empty($order->date_fulfilled) ? 'active' : '' }}">
				<i class="fas fa-shipping-fast"></i>
				<h6>Shipped</h6>
			</div>
		</div>
		<br><br>
		<div class="row">
			<div class="col ">
				<p class="caption">Order reference:</p>
				<h4 class="text-bold with-underline">{{$order->hash}}</h4>
			</div>
			<div class="col ">
				<p class="caption">Date ordered:</p>
				<h4>{{$order->date_ordered}}</h4>
			</div>
			<div class="col ">
				<p class="caption">Company:</p>
				<h4>{{$order->company->name}}</h4>	
			</div>
		</div>		
		<br><br><br>
		<div class="row">
			<div class="col">
				<h4 class="text-bold">CUSTOMER INFORMATION:</h4>	
				<p class="caption">Customer name:</p>
				<h4>{{$order->first_name}} {{$order->last_name}}</h4>
				<p class="caption">Email:</p>
				<h4>{{$order->email}}</h4>
				<p class="caption">Contact number:</p>
				<h4>{{$order->country_code}} {{$order->contact_number}}</h4>
				<p class="caption">Shipping address:</p>
				<h4>{{$order->shipping_address_1}}</h4>
				@if(!empty($order->shipping_address_2))
					<h4>{{$order->shipping_address_2}}</h4>
				@endif
			</div>
		</div>
		<br><br><br>
		<div class="row">
			<div class="col">
				<h4 class="text-bold">CART:</h4>
				<table class="table shipping">
                    <thead>
                        <th>Item</th>
                        <th class="text-right" style="width:100px;">Quantity</th>
                        <th class="text-right">Total</th>
                    </thead>
                    <tbody>
                        @foreach($order->order_items as $item)
                            <tr>
                                <td class="align-middle">{{$item->product_name}}<br><span class="text-grey">{{$item->variant_description}}</span></td>
                                <td class="text-right align-middle">{{$item->quantity}}</td>
                                <td class="text-right align-middle">{{$order->company->currency . " " . number_format($item->price, 2, '.', ',')}}</td>
                            </tr>
                        @endforeach
                        <tr class="">
                            <td colspan="2"><span class="note">SHIPPING:<br></span>{{$order->shipping_method}}</td>
                            <td class="text-right align-middle">{{$order->view_shipping_price()}}</td>
                        </tr>
                        <tr class="text-bold">
                            <td colspan="2">Total amount due:</td>
                            <td class="text-right align-middle">{{$order->total()}}</td>
                        </tr>
                    </tbody>
                </table>
			</div>

		</div>
	</div>
@endsection