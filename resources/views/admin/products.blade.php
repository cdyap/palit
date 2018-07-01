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
	@if($products_with_problems->count() > 0)
		<a href="" class="button z-depth-1 error" data-toggle="modal" data-target="#errors">View errors</a>

		<div class="modal product-modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="errors" aria-labelledby="Error Modal" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">PRODUCTS WITH ERRORS</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<h6>The following products have variant columns but do not have variants:</h6>
								<table class="table">
									<thead>
										<tr>
											<th>Name</th>
											<th>Description</th>
											<th style="min-width:100px;"> </th>
										</tr>
									</thead>	
									<tbody>
										@foreach($products_with_problems as $product)
											<tr>
												<td><a href="/products/{{$product->slug}}">{{$product->name}}</a></td>
												<td>{{$product->description}}</td>
												<td style="white-space: nowrap"><a href="/products/{{$product->slug}}#AddVariant" class="button">Add variant</a></td>
											</tr>
										@endforeach
									</tbody>								
								</table>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	@endif
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
									<td class="text-right">{{$product->available_inventory}}</td>
									<td class="text-right">{{$product->variants->count()}}</td>
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
								<td class="text-right">{{$product->available_inventory}}</td>
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