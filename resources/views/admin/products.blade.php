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
	<a href="/products/new" class="button z-depth-1">Add product</a>
	<br><br>
	<div class="row">
		<div class="col-lg-12">
			@if($available_products->count() > 0)
			<div class="block table-responsive-sm">
				<h4>Available products</h4>
					<table class="table">
						<thead>
							<tr>
								<th>Name</th>
								<th style="width:50vw;">Description</th>
								<th>Shipping</th>
								<th class="text-right" style="max-width:100px;">Inventory</th>
								<th class="text-right" style="max-width:100px;">Variants</th>
							</tr>
						</thead>
						<tbody>
							@foreach($available_products as $product)
								<tr>
									<td><a href="/products/{{$product->slug}}">{{$product->name}}</a></td>
									<td>{!! nl2br(e($product->description)) !!}</td>
									<td>{{($product->is_shipped) ? "Required" : ""}}</td>
									<td class="text-right">{{$product->total_inventory}}</td>
									<td class="text-right">{{$product->variants()->count()}}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
			</div>
			@endif
		</div>

		<div class="col-lg-12">
			@if($unavailable_products->count() > 0)
			<div class="block table-responsive-sm">
				<h4>Unavailable products</h4>
				<table class="table" style="">
					<thead>
						<tr>
							<th>Name</th>
								<th style="width:50vw;">Description</th>
								<th>Shipping</th>
								<th class="text-right" style="max-width:100px;">Inventory</th>
								<th class="text-right" style="max-width:100px;">Variants</th>
						</tr>
					</thead>
					<tbody>
						@foreach($unavailable_products as $product)
							<tr>
								<td><a href="/products/{{$product->slug}}">{{$product->name}}</a></td>
								<td>{!! nl2br(e($product->description)) !!}</td>
								<td>{{($product->is_shipped) ? "Required" : ""}}</td>
								<td class="text-right">{{$product->total_inventory}}</td>
								<td class="text-right">{{$product->variants()->count()}}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			@endif
		</div>
	</div>
@endsection