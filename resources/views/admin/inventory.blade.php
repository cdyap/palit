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
	<a href="/inventory/new" class="button z-depth-1">Add stocks</a>
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
								<p class="note">The following products have errors. You may not add deliveries for these products.</p>
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
	<br><br><br>
	<div class="row">
		<div class="col-lg-10 col-md-12">
			@if($products->count() > 0)
				<div class="table-responsive-sm block" style="margin-top:0">
					<h4 class="with-underline">Current inventory per product</h4>
					<p class="note">Click a row to show variant details</p>
					<table class="table inventory-table">
						<thead>
							<tr>
								<th>Name</th>
								<th class="text-right" style="max-width:100px;">Inventory</th>
								<th class="text-right" style="max-width:100px;">Incoming</th>
								<th class="text-right" style="max-width:100px;">Confirmed</th>
								<th class="text-right" style="max-width:100px;">Unconfirmed</th>
							</tr>
						</thead>
						<tbody>
							@foreach($products as $product)
								<tr data-product="{{$product->id}}">
									<td>{{$product->name}}</td>
									<td class="text-right">{{$product->available_inventory}}</td>
									<td class="text-right">{{$product->incoming_inventory}}</td>
									<td class="text-right">{{$paid_orders->where('product_id', $product->id)->sum('quantity')}}</td>
									<td class="text-right">{{$unpaid_orders->where('product_id', $product->id)->sum('quantity')}}</td>
								</tr>
								@if($product->variants->count() > 0)
									@foreach($product->variants->sortBy('position') as $variant)
										<tr class="variant_{{$variant->id}}" data-id="{{$variant->id}}" data-inventory="{{$variant->inventory}}" data-price="{{$variant->price}}" data-itemId="{{{ $variant->id }}}"  style="background:#F8F9FA"> 
											<td>&nbsp;&nbsp;
												<?php $variant_description = array(); ?>

												@foreach($product_variant_columns->where('name', 'variant_'.$product->id)->sortBy('value_2') as $column)
													<?php $variant_description[] = $column->value .": ".$variant->{$column->value_2}; ?>
												@endforeach
												{{collect($variant_description)->implode("; ")}}
											</td>
											<td class="text-right">{{$variant->available_inventory}}</td>
											<td class="text-right">{{$variant->incoming_inventory}}</td>
											<td class="text-right">{{$paid_orders->where('variant_id', $variant->id)->sum('quantity')}}</td>
											<td class="text-right">{{$unpaid_orders->where('variant_id', $variant->id)->sum('quantity')}}</td>
										</tr>
									@endforeach
								@endif
								
							@endforeach
						</tbody>
					</table>
				</div>
			@endif
			{{-- @foreach($products as $product)
				@if($product->variants->count() > 0)
					<div class="modal product-modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="productModal{{$product->slug}}" aria-labelledby="product Modal" aria-hidden="true">
						<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title with-underline" id="exampleModalLabel">{{$product->name}} INVENTORY</h4>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body" style="margin-top:-30px;">
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col">
													<p class="caption">INVENTORY:</p>
													<h5>{{$product->available_inventory}}</h5>
												</div>
												<div class="col">
													<p class="caption">INCOMING:</p>
													<h5>{{$product->incoming_inventory}}</h5>
												</div>
												<div class="col">
													<p class="caption">CONFIRMED ORDERS:</p>
													<h5>{{$paid_orders->where('product_id', $product->id)->pluck('order_items')->count()}}</h5>
												</div>
												<div class="col">
													<p class="caption">UNCONFIRMED ORDERS:</p>
													<h5>{{$unpaid_orders->where('product_id', $product->id)->pluck('order_items')->count()}}</h5>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="product-variants-block">
												<p class="caption">VARIANTS:</p>
												<table class="table">
													<thead>
														<tr>
															@foreach($product_variant_columns->where('name', 'variant_'.$product->id)->sortBy('value_2') as $column)
																<th>{{$column->value}}</th>
															@endforeach
															<th class="text-right">Inventory</th>
															<th class="text-right">Incoming</th>
															<th class="text-right">Price</th>
														</tr>
													</thead>
													<tbody data-entityname="variants">
														@foreach($product->variants->sortBy('position') as $variant)
															<tr class="variant_{{$variant->id}}" data-id="{{$variant->id}}" data-inventory="{{$variant->inventory}}" data-price="{{$variant->price}}" data-itemId="{{{ $variant->id }}}">
																@foreach($product_variant_columns->where('name', 'variant_'.$product->id)->sortBy('value_2') as $column)
																	<td class="{{$column->value_2}}">{{ $variant->{$column->value_2} }}</td>
																@endforeach
																
																<td class="text-right">{{$variant->available_inventory}}</td>
																<td class="text-right">{{$variant->incoming_inventory}}</td>
																<td class="text-right">{{$product->currency}} {{$variant->view_price}}</td>
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
					</div>
				@else
					<div class="modal product-modal fade bd-example-modal-md" tabindex="-1" role="dialog" id="productModal{{$product->slug}}" aria-labelledby="product Modal" aria-hidden="true">
						<div class="modal-dialog modal-md modal-dialog-centered" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title with-underline" id="exampleModalLabel" style="color: black;">{{$product->name}} INVENTORY</h4>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body" style="margin-top: -40px;">
									<div class="row">
										<div class="col">
											<p class="caption">INVENTORY:</p>
											<h5>{{$product->available_inventory}}</h5>
										</div>
										<div class="col">
											<p class="caption">INCOMING:</p>
											<h5>{{$product->incoming_inventory}}</h5>
										</div>
										<div class="col">
											<p class="caption">ORDERS:</p>
											<h5></h5>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				@endif
			@endforeach --}}
		</div>
	</div>
	<div class="row">
		<div class="col-lg-10 col-md-12">
			@if($pending_deliveries->count() > 0)
				<div class="table-responsive-sm block">
					<h4 class="with-underline">Pending deliveries</h4>
					<p class="note">Click a row to show delivery details</p>
					<table class="table table-hover inventory-table table-striped">
						<thead>
							<tr>
								<th>Supplier</th>
								<th class="text-right">Delivery date</th>
								<th class="text-right" style="max-width:100px;">Items in cart</th>
								<th class="text-right" style="max-width:100px;">Total quantity</th>
								<th class="text-right" style="max-width:100px;">Received</th>
								<th style="width: 50px;"></th>
							</tr>
						</thead>
						<tbody>
							@foreach($pending_deliveries as $delivery)
								<tr data-product="{{$delivery->id}}" >
									<td data-toggle="modal" data-target="#deliveryModal{{$delivery->id}}">{{$delivery->supplier}}</td>
									<td data-toggle="modal" data-target="#deliveryModal{{$delivery->id}}" class="text-right">{{$delivery->expected_arrival}} ({{$delivery->remaining_days}})</td>
									<td data-toggle="modal" data-target="#deliveryModal{{$delivery->id}}" class="text-right">{{$delivery->items_in_cart}}</td>
									<td data-toggle="modal" data-target="#deliveryModal{{$delivery->id}}" class="text-right">{{$delivery->delivered_products->sum('quantity') + $delivery->delivered_variants->sum('quantity')}}</td>
									<td data-toggle="modal" data-target="#deliveryModal{{$delivery->id}}" class="text-right">{{$delivery->delivered_products->sum('delivered_quantity') + $delivery->delivered_variants->sum('delivered_quantity')}}</td>
									<td>
										<div class="btn-group dropright float-right">
											<div class="option-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="fas fa-ellipsis-v"></i>
											</div>
											<div class="dropdown-menu">
												<a class="dropdown-item receive_delivery" data-toggle="modal" data-target="#deliveryModal{{$delivery->id}}">Receive delivery</a>
												<div class="dropdown-divider"></div>
												<a class="dropdown-item delete" data-toggle="modal" data-target="#deleteDelivery{{$delivery->id}}">Delete delivery</a>
											</div>
										</div>
									</td>
								</tr>

								<!-- Delete delivery modal -->
								<div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="deleteDelivery{{$delivery->id}}" aria-labelledby="deleteDelivery" aria-hidden="true">
									<div class="modal-dialog modal modal-dialog-centered" role="document">
										<div class="modal-content ">
											<form action="/inventory/{{$delivery->id}}/delete" method="POST" class="delete_delivery_form">
												<input type="hidden" name="_token" value="{{ csrf_token() }}">
												<input type="hidden" name="_method" value="delete" />
												<div class="modal-header">
													<h5 class="modal-title" id="exampleModalLabel">Delete this delivery?</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
													</button>
												</div>
													<div class="modal-body">
														<h5>Are you sure you want to delete this delivery? This cannot be undone.</h5>
														
													</div>
												<div class="modal-footer">
													<button type="button" class="button secondary" data-dismiss="modal">Cancel</button>
													<button type="submit" class="button delete">Delete</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							@endforeach
						</tbody>
					</table>
					@foreach($pending_deliveries as $delivery)
						<!-- Delivery details modal -->
						<div class="modal fade bd-example-modal-lg delivery-modal" tabindex="-1" role="dialog" id="deliveryModal{{$delivery->id}}" aria-labelledby="Delivery modal" aria-hidden="true">
							<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
								<div class="modal-content ">
									<div class="modal-header">
										<h4 class="modal-title with-underline" id="exampleModalLabel">Delivery from {{$delivery->supplier}}</h4>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body" style="margin-top:-30px;">
										<div class="row">
											<div class="col">
												<p class="caption">DELIVERY DATE:</p>
												<h5>{{$delivery->expected_arrival}}</h5>
											</div>
											<div class="col">
												<p class="caption">DAYS REMAINING:</p>
												<h5>{{$delivery->remaining_days}}</h5>
											</div>
											<div class="col">
												<p class="caption">ORDERS:</p>
												<h5></h5>
											</div>
										</div>
										<div class="row">
											<div class="col">
												<p class="caption">DELIVERY CART:</p>
												<form action="/inventory/delivery/{{$delivery->id}}/receive" method="POST" >
													<input type="hidden" name="_token" value="{{ csrf_token() }}">
													<table class="table table-striped">
														<thead>
															<tr>
																<th>Product</th>
																<th>Variant</th>
																<th class="text-right">Incoming</th>
																<th class="text-right">Received</th>
																<th class="text-right" style="width:120px;">Quantity</th>
															</tr>
														</thead>
														<tbody>
															@foreach($delivery->delivered_products as $delivered_product)
																<tr>
																	<td>{{$delivered_product->product->name}}</td>
																	<td></td>
																	<td class="text-right">{{$delivered_product->quantity}}</td>
																	<td class="text-right">{{$delivered_product->delivered_quantity}}</td>
																	<td><input type="number" class="text-right form-control product-quantity" name="delivered_product[{{$delivered_product->id}}]" min="0" max="{{$delivered_product->quantity - $delivered_product->delivered_quantity}}" value="{{$delivered_product->quantity - $delivered_product->delivered_quantity}}"></td>
																</tr>
															@endforeach
															@foreach($delivery->delivered_variants as $delivered_variant)
																<tr>
																	<td>{{$delivered_variant->product->name}}</td>
																	<?php $i = 0; ?>
																	<?php $description = ""; ?>
																	@foreach($product_variant_columns->where('name', 'variant_'.$delivered_variant->product->id) as $column)
																		<?php $i = $i + 1; ?>
																		@if($i != 1)
																			<?php $description = $description . ", "; ?>
																		@endif
																		<?php $description = $description . $column->value . ": " . $delivered_variant->variant->{"attribute_" . $i}; ?>
																	@endforeach

																	<td>{{$description}}</td>
																	<td class="text-right">{{$delivered_variant->quantity}}</td>
																	<td class="text-right">{{$delivered_variant->delivered_quantity}}</td>
																	<td><input type="number" class="text-right form-control product-quantity" name="delivered_variant[{{$delivered_variant->id}}]" min="0" max="{{$delivered_variant->quantity - $delivered_variant->delivered_quantity}}" value="{{$delivered_variant->quantity - $delivered_variant->delivered_quantity}}"></td>
																</tr>
															@endforeach
															<tr style="border-top: 2px solid black">
																<td></td>
																<td class="text-right text-bold">Total:</td>
																<td class="text-right text-bold">{{$delivery->delivered_products->sum('quantity') + $delivery->delivered_variants->sum('quantity')}}</td>
																<td class="text-right text-bold">{{$delivery->delivered_products->sum('delivered_quantity') + $delivery->delivered_variants->sum('delivered_quantity')}}</td>
																<td></td>
															</tr>
															
														</tbody>
													</table>
													<br>
													<button type="submit" class="button">Receive</a>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					@endforeach
				</div>
				
			@endif

			@if($completed_deliveries->count() > 0)
				<div class="table-responsive-sm block">
					<h4 class="with-underline">Completed deliveries</h4>
					<p class="note">Click a row to show delivery details</p>
					<table class="table table-hover inventory-table table-striped">
						<thead>
							<tr>
								<th>Supplier</th>
								<th class="text-right">Delivery date</th>
								<th class="text-right" style="max-width:100px;">Items in cart</th>
								<th class="text-right" style="max-width:150px;">Total quantity received</th>
								<th style="width: 50px;"></th>
							</tr>
						</thead>
						<tbody>
							@foreach($completed_deliveries as $delivery)
								<tr data-product="{{$delivery->id}}" >
									<td data-toggle="modal" data-target="#deliveryModal{{$delivery->id}}">{{$delivery->supplier}}</td>
									<td data-toggle="modal" data-target="#deliveryModal{{$delivery->id}}" class="text-right">{{$delivery->expected_arrival}} ({{$delivery->remaining_days}})</td>
									<td data-toggle="modal" data-target="#deliveryModal{{$delivery->id}}" class="text-right">{{$delivery->items_in_cart}}</td>
									<td data-toggle="modal" data-target="#deliveryModal{{$delivery->id}}" class="text-right">{{$delivery->delivered_products->sum('delivered_quantity') + $delivery->delivered_variants->sum('delivered_quantity')}}</td>
									<td>
										<div class="btn-group dropright float-right">
											<div class="option-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="fas fa-ellipsis-v"></i>
											</div>
											<div class="dropdown-menu">
												<a class="dropdown-item delete" data-toggle="modal" data-target="#deleteDelivery{{$delivery->id}}">Delete delivery</a>
											</div>
										</div>
									</td>
								</tr>

								<!-- Delete delivery modal -->
								<div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="deleteDelivery{{$delivery->id}}" aria-labelledby="deleteDelivery" aria-hidden="true">
									<div class="modal-dialog modal modal-dialog-centered" role="document">
										<div class="modal-content ">
											<form action="/inventory/{{$delivery->id}}/delete" method="POST" class="delete_delivery_form">
												<input type="hidden" name="_token" value="{{ csrf_token() }}">
												<input type="hidden" name="_method" value="delete" />
												<div class="modal-header">
													<h5 class="modal-title" id="exampleModalLabel">Delete this delivery?</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
													</button>
												</div>
													<div class="modal-body">
														<h5>Are you sure you want to delete this delivery? This cannot be undone.</h5>
														
													</div>
												<div class="modal-footer">
													<button type="button" class="button secondary" data-dismiss="modal">Cancel</button>
													<button type="submit" class="button delete">Delete</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							@endforeach
						</tbody>
					</table>
					@foreach($completed_deliveries as $delivery)
						<!-- Delivery details modal -->
						<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="deliveryModal{{$delivery->id}}" aria-labelledby="Delivery modal" aria-hidden="true">
							<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
								<div class="modal-content ">
									<div class="modal-header">
										<h4 class="modal-title with-underline" id="exampleModalLabel">Delivery from {{$delivery->supplier}}</h4>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body" style="margin-top:-30px;">
										<div class="row">
											<div class="col">
												<p class="caption">DELIVERY DATE:</p>
												<h5>{{$delivery->expected_arrival}}</h5>
											</div>
											<div class="col">
												<p class="caption">DAYS REMAINING:</p>
												<h5>{{$delivery->remaining_days}}</h5>
											</div>
											<div class="col">
												<p class="caption">ORDERS:</p>
												<h5></h5>
											</div>
										</div>
										<div class="row">
											<div class="col">
												<p class="caption">DELIVERY CART:</p>
												<table class="table table-striped">
													<thead>
														<tr>
															<th>Product</th>
															<th>Variant</th>
															<th class="text-right">Received</th>
														</tr>
													</thead>
													<tbody>
														@foreach($delivery->delivered_products as $delivered_product)
															<tr>
																<td>{{$delivered_product->product->name}}</td>
																<td></td>
																<td class="text-right">{{$delivered_product->delivered_quantity}}</td>
															</tr>
														@endforeach
														@foreach($delivery->delivered_variants as $delivered_variant)
															<tr>
																<td>{{$delivered_variant->product->name}}</td>
																<?php $i = 0; ?>
																<?php $description = ""; ?>
																@foreach($product_variant_columns->where('name', 'variant_'.$delivered_variant->product->id) as $column)
																	<?php $i = $i + 1; ?>
																	@if($i != 1)
																		<?php $description = $description . ", "; ?>
																	@endif
																	<?php $description = $description . $column->value . ": " . $delivered_variant->variant->{"attribute_" . $i}; ?>
																@endforeach

																<td>{{$description}}</td>
																<td class="text-right">{{$delivered_variant->delivered_quantity}}</td>
															</tr>
														@endforeach
														<tr style="border-top: 2px solid black">
															<td></td>
															<td class="text-right text-bold">Total:</td>
															<td class="text-right text-bold">{{$delivery->delivered_products->sum('delivered_quantity') + $delivery->delivered_variants->sum('delivered_quantity')}}</td>
														</tr>
														
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			@endif
		</div>
	</div>
@endsection