@extends('layouts.admin')

@section('content')
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
			{!!session('error')!!}
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
	@endif
	</div>
	<br>
	<h5 class="tabs"><a href="/orders" class="{{$order_type == 'Unpaid' ? 'active' : ''}}">Unpaid</a> <a href="/orders/paid" class="{{$order_type == 'Paid' ? 'active' : ''}}">Paid</a> <a href="/orders/shipped" class="{{$order_type == 'Shipped' ? 'active' : ''}}">Shipped</a> <a href="/orders/cancelled" class="{{$order_type == 'Cancelled' ? 'active' : ''}}">Cancelled</a></h5>
	@if($orders->count() > 0)
		<table class="table table-striped table-hover order-table">
			<thead>
				<tr>
					<th>Hash</th>
					<th>Name</th>
					<th>Shipping address</th>
					<th class="text-right">Total amount</th>
					<th class="text-right">Ordered on</th>
				</tr>
			</thead>
			<tbody>
				@foreach($orders as $order) 
					<tr data-order="{{$order->hash}}" data-toggle="modal" data-target="#orderModal{{$order->hash}}" class="hover-pointer">
						<td>{{$order->hash}}</td>
						<td>{{$order->first_name . " " . $order->last_name}}</td>
						<td>{{$order->shipping_address_1 . $order->shipping_address_2}}</td>
						@if(!$order->trashed())
                            <td class="text-right">{{$order->total}}</td>
                        @else
                        	<td class="text-right">{{$order->total_trashed}}</td>
                        @endif    
						<td class="text-right">{{$order->date_ordered}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		<nav aria-label="Orders navigation" class="justify-content-center">
			{{ $orders->links("pagination::bootstrap-4") }}
		</nav>
		
		@foreach($orders as $order) 
			<div class="modal product-modal fade bd-example-modal-lg with-partition" tabindex="-1" role="dialog" id="orderModal{{$order->hash}}" aria-labelledby="Order Modal" aria-hidden="true">
				<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">{{$order_type}} ORDER</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="card-partition">
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
										<p class="caption">Payment method:</p>
										<h4>{{$order->payment_method}}</h4>	
									</div>
								</div>		
								<br><br>
								<div class="row">
									<div class="col">
										<h5 class="text-bold">CUSTOMER INFORMATION:</h5>	
										<p class="caption">Customer name:</p>
										<h5>{{$order->first_name}} {{$order->last_name}}</h5>
										<p class="caption">Email:</p>
										<h5>{{$order->email}}</h5>
										<p class="caption">Contact number:</p>
										<h5>{{$order->country_code}} {{$order->contact_number}}</h5>
										<p class="caption">Shipping address:</p>
										<h5>{{$order->shipping_address_1}}</h5>
										@if(!empty($order->shipping_address_2))
											<h5>{{$order->shipping_address_2}}</h5>
										@endif
									</div>
								</div>
								@if(empty($order->date_paid) && !$order->trashed())
									<br><br>								
									<form action="/orders/{{$order->hash}}/confirm" method="POST" >
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<button type="submit">Confirm payment</button>
									</form>
									<br><br>
									<form action="/orders/{{$order->hash}}/delete" method="POST" class="delete-order-form">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<input type="hidden" name="_method" value="delete" />
										<div class="form-group">
											<label for="name">To cancel this order, enter the order reference</label>
											<input type="text" name="hash" class="form-control {{ $errors->has('hash') ? 'has-error' : ''}}" required value="{{ old('hash') }}" style="width:120px">
										</div>
										<button class="ghost button hover-red delete-order-button">Cancel order</button>
									</form>
								@elseif(!empty($order->date_paid) && empty($order->date_fulfilled) && !$order->trashed())
									<p class="caption">Payment received:</p>
									<h5>{{$order->date_paid}}</h5>
									<br><br>								
									<form action="/orders/{{$order->hash}}/fulfill" method="POST">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<button type="submit">Ship out</button>
									</form>
								@elseif(!$order->trashed())
									<p class="caption">Payment received:</p>
									<h5>{{$order->date_paid}}</h5>
									<p class="caption">Shipped out on:</p>
									<h5>{{$order->date_fulfilled}}</h5>
								@else
									<p class="caption">Cancelled on:</p>
									<h5>{{$order->deleted_at}}</h5>
								@endif
								<br>
							</div>
							<div class="card-subpartition">
								<div class="row">
									<div class="col">
										<h4 class="text-bold">CART:</h4>
										<table class="table shipping">
						                    <thead>
						                        <th>Item</th>
						                        <th class="text-right" style="width:50px;">Quantity</th>
						                        <th class="text-right">Price</th>
						                        <th class="text-right" >Total</th>
						                    </thead>
						                    <tbody>
						                    	@if(!$order->trashed())
							                        @foreach($order->order_items as $item)
							                            <tr>
							                                <td class="align-middle">{{$item->product_name}}<br><span class="text-grey">{{$item->variant_description}}</span></td>
							                                <td class="text-right align-middle">{{$item->quantity}}</td>
							                                <td class="text-right align-middle">{{$order->company->currency . " " . number_format($item->price, 2, '.', ',')}}</td>
							                                <td class="text-right align-middle">{{$order->company->currency . " " . number_format($item->total_price, 2, '.', ',')}}</td>
							                            </tr>
							                        @endforeach
						                        @else
							                        @foreach($order->order_items_trashed as $item)
							                            <tr>
							                                <td class="align-middle">{{$item->product_name}}<br><span class="text-grey">{{$item->variant_description}}</span></td>
							                                <td class="text-right align-middle">{{$item->quantity}}</td>
							                                <td class="text-right align-middle">{{$order->company->currency . " " . number_format($item->price, 2, '.', ',')}}</td>
							                                <td class="text-right align-middle">{{$order->company->currency . " " . number_format($item->total_price, 2, '.', ',')}}</td>
							                            </tr>
							                        @endforeach
						                        @endif
						                        <tr class="">
						                            <td colspan="2"><span class="note">SHIPPING:<br></span>{{$order->shipping_method}}</td>
						                            <td></td>
						                            <td class="text-right align-middle">{{$order->view_shipping_price()}}</td>
						                        </tr>
						                        <tr class="text-bold">
						                            <td colspan="2">Total amount due:</td>
						                            <td></td>
						                            @if(!$order->trashed())
							                            <td class="text-right align-middle">{{$order->total}}</td>
						                            @else
						                            	<td class="text-right align-middle">{{$order->total_trashed}}</td>
							                        @endif    
						                        </tr>
						                    </tbody>
						                </table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		@endforeach
	@endif
@endsection