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
	</div>
	<br>
	<br>
	<a href="/{{$company->slug}}">Order page</a>
	@if($orders->count() > 0)
		<table class="table">
			<thead>
				<tr>
					<th>Email</th>
					<th>Name</th>
					<th>Shipping address</th>
					<th>No. of items</th>
					<th>Total amount</th>
					<th>Ordered on</th>
				</tr>
			</thead>
			<tbody>
				@foreach($orders as $order) 
					<tr>
						<td>{{$order->email}}</td>
						<td>{{$order->first_name . " " . $order->last_name}}</td>
						<td>{{$order->shipping_address_1 . $order->shipping_address_2}}</td>
						<td>{{$order->order_items()->sum('quantity')}}</td>
						<td>{{$order->order_items()->sum('price')}}</td>
						<td>{{$order->date_ordered}}</td>
					</tr>
				@endforeach
			</tbody>
			
		</table>
		
	@endif
@endsection