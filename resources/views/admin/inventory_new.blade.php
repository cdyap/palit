@extends('layouts.admin')

@section('content')
	<br>
	<h5><a href="/inventory">< Inventory</a></h5>
	<br>
	<div class="alerts-holder">
	@if(session('success'))
		<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>
			<strong>{{session('emphasize')}}</strong> {{session('success')}}
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
	@endif
	@if ($errors->any())
	    <div class="alert alert-error fade show z-depth-1-half" role="alert">
            @foreach ($errors->all() as $error)
                {{ $error }} <br>
            @endforeach
	    </div>
	@endif
	</div>
	<div class="row">
		<div class="col-lg-10 col-md-12">
			@if($products_with_variants->count() > 0 || $products_wo_variants->count() > 0)
				<form action="/inventory/store" method="POST">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="block">
						<h4>Delivery details</h4>
						<br>
						<br>
						<div class="form-row">
							<div class="col-xs-10 col-sm-9 form-group">
								<label for="date">Supplier:*</label>
								<input type="text" name="supplier" id="supplier" class="form-control {{ $errors->has('supplier') ? 'has-error' : ''}}"  value="{{ old('supplier') }}" required>
							</div>
						</div>
						<div class="form-row">
							<div class="col-xs-8 col-sm-6 form-group">
								<label for="date">Expected date of arrival:*</label>
								<input type="date" name="expected_arrival" id="expected_arrival" class="form-control datepicker {{ $errors->has('expected_arrival') ? 'has-error' : ''}}" required value="{{ old('expected_arrival') }}" style="background: white!important">
							</div>
						</div>
					</div>
					<div class="table-responsive-sm block" style="margin-top:0">
						<h4 class="with-underline">Product inventory</h4>
						<p class="note">Enter quantity of products to deliver</p>
						<table class="table inventory-table">
							<thead>
								<tr>
									<th>Name</th>
									<th class="text-right" style="max-width:100px;">Inventory</th>
									<th class="text-right" style="max-width:100px;">Incoming</th>
									<th class="text-right" style="max-width:100px;">Confirmed</th>
									<th class="text-right" style="max-width:100px;">Unconfirmed</th>
									<th class="text-right" style="max-width:100px;">Quantity</th>
								</tr>
							</thead>
							<tbody>
								@if($products_wo_variants->count() > 0)
									<tr>
										<td colspan="6" class="text-underline text-bold">Without variants</td>
									</tr>
									@foreach($products_wo_variants as $product)
										<tr data-product="{{$product->id}}" class="align-middle">
											<td class="align-middle">{{$product->name}}</td>
											<td class="text-right align-middle">{{$product->available_inventory}}</td>
											<td class="text-right align-middle">{{$product->incoming_inventory}}</td>
											<td class="text-right align-middle">{{$paid_orders->where('product_id', $product->id)->sum('quantity')}}</td>
											<td class="text-right align-middle">{{$unpaid_orders->where('product_id', $product->id)->sum('quantity')}}</td>
											<td><input type="number" min="0" class="form-control text-right float-right input-stockup" name="product[{{$product->id}}]" style="width:100px;" data-description="{{$product->name}}" value=""></td>
										</tr>
									@endforeach								
								@endif
								@if($products_with_variants->count() > 0)
									<tr>
										<td colspan="6" class="text-underline text-bold">With variants</td>
									</tr>
									@foreach($products_with_variants as $product)
										<tr data-product="{{$product->id}}">
											<td>{{$product->name}}</td>
											<td class="text-right">{{$product->available_inventory}}</td>
											<td class="text-right">{{$product->incoming_inventory}}</td>
											<td class="text-right">{{$paid_orders->where('product_id', $product->id)->sum('quantity')}}</td>
											<td class="text-right">{{$unpaid_orders->where('product_id', $product->id)->sum('quantity')}}</td>
											<td></td>
										</tr>
										@foreach($product->variants->sortBy('position') as $variant)
											<tr class="variant_{{$variant->id}}" data-id="{{$variant->id}}" data-inventory="{{$variant->inventory}}" data-price="{{$variant->price}}" data-itemId="{{{ $variant->id }}}"  style="background:#F8F9FA"> 
												<td class="align-middle">&nbsp;&nbsp;
													<?php $variant_description = array(); ?>

													@foreach($product_variant_columns->where('name', 'variant_'.$product->id)->sortBy('value_2') as $column)
														<?php $variant_description[] = $column->value .": ".$variant->{$column->value_2}; ?>
													@endforeach
													{{collect($variant_description)->implode("; ")}}
												</td>
												<td class="text-right align-middle">{{$variant->available_inventory}}</td>
												<td class="text-right align-middle">{{$variant->incoming_inventory}}</td>
												<td class="text-right align-middle">{{$paid_orders->where('variant_id', $variant->id)->sum('quantity')}}</td>
												<td class="text-right align-middle">{{$unpaid_orders->where('variant_id', $variant->id)->sum('quantity')}}</td>
												<td><input type="number" min="0" class="form-control text-right float-right input-stockup" name="variant[{{$variant->id}}]" style="width:100px;" data-description="{{$product->name . " / " . collect($variant_description)->implode("; ")}}"  value=""></td>
											</tr>
										@endforeach
									@endforeach
								@endif
							</tbody>
						</table>
					</div>
					<button type="button" data-toggle="modal" data-target="#confirmProductsModal">Confirm products for stocking</button>
					<div class="modal fade bd-example-modal with-partition" tabindex="-1" role="dialog" id="confirmProductsModal" aria-labelledby="deleteVariant" aria-hidden="true">
						<div class="modal-dialog modal modal-dialog-centered" role="document">
							<div class="modal-content ">
								<div class="modal-header">
									<h5 class="modal-title" id="exampleModalLabel">Confirm products for stocking</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
									<div class="modal-body">
										<div class="card-partition">
											<p class="caption" style="margin-top:0;">SUPPLIER:</p>
											<h5 id="confirm-supplier" class="text-red">None entered</h5>
											<p class="caption">EXPECTED DATE OF ARRIVAL:</p>
											<h5 id="confirm-date-arrival" class="text-red">None entered</h5>
										</div>
										<div class="card-subpartition">
											<div class="row">
												<div class="col">
													<h4 class="text-bold">PRODUCTS FOR DELIVERY:</h4>
													<table class="table confirm-products">
														<thead>
															<th>Product</th>
															<th class="text-right">Quantity</th>
														</thead>
														<tbody>
															
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								<div class="modal-footer">
									<button type="button" class="button secondary" data-dismiss="modal">Cancel</button>
									<button type="submit" class="button confirm-products-submit">Stock up products</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			@else
				<h4>No products added.</h4>
			@endif
		</div>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js">
@endsection

@section('custom_js')	
	<script type="text/javascript">
	// $(window).bind('beforeunload', function(){
	// 	return true;
	// });
	</script>
@endsection