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
			<strong>{{session('emphasize')}}</strong> {{session('error')}}
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
	@endif
	</div>
	<br>
	<h5><a href="/inventory">< Inventory</a></h5>
	<div class="row">
		<div class="col">
			<p class="caption">DELIVERY DATE:</p>
			<h5>{{$delivery->expected_arrival}}</h5>
		</div>
		<div class="col">
			<p class="caption">DAYS REMAINING:</p>
			<h5>{{Carbon\Carbon::parse($delivery->expected_arrival)->diffInDays(Carbon\Carbon::now())}}</h5>
		</div>
		<div class="col">
			<p class="caption">ITEMS IN CART:</p>
			<h5>{{$delivery->delivered_products->count() + $delivery->delivered_variants->count()}}</h5>
		</div>
		<div class="col">
			<p class="caption">TOTAL QUANTITY:</p>
			<h5>{{$delivery->delivered_products->sum('quantity') + $delivery->delivered_variants->sum('quantity')}}</h5>
		</div>
		<div class="col">
			<p class="caption">TOTAL RECEIVED:</p>
			<h5>{{$delivery->delivered_products->sum('delivered_quantity') + $delivery->delivered_variants->sum('delivered_quantity')}}</h5>
		</div>
	</div>
	<div class="row">
		<div class="col">
			@if($delivery->delivered_products->where('is_delivered', false)->count() + $delivery->delivered_variants->where('is_delivered', false)->count() > 0)
				<form action="/inventory/delivery/{{$delivery->slug}}/receive" method="POST">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<!-- Pending deliveries -->
					<div class="block">
						<h4>Delivery cart:</h4>
						<p class="note">To receive items, enter quantities into the 'Received quantity' column.</p>
						<table class="table">
							<thead>
								<tr>
									<th>Product</th>
									<th>Variant</th>
									<th class="text-right">Incoming</th>
									<th class="text-right">Received</th>
									<th class="text-right" style="width:200px">Receive quantity</th>
								</tr>
							</thead>
							<tbody>
								@foreach($delivery->delivered_products->where('is_delivered', false) as $delivered_product)
									<tr>
										<td>{{$delivered_product->product->name}}</td>
										<td></td>
										<td class="text-right">{{$delivered_product->quantity}}</td>
										<td class="text-right">{{$delivered_product->delivered_quantity}}</td>
										<td class="text-right" style="padding-left:50px;"><input type="number" class="form-control text-right" min="0" name="delivered_product[{{$delivered_product->id}}][delivered_quantity]" value="{{$delivered_product->quantity - $delivered_product->delivered_quantity}}"></td>
									</tr>
								@endforeach
								@foreach($delivery->delivered_variants->where('is_delivered', false) as $delivered_variant)
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
										<td class="text-right" style="padding-left:50px;"><input type="number" class="form-control text-right" min="0" name="delivered_variant[{{$delivered_variant->id}}][delivered_quantity]" value="{{$delivered_variant->quantity - $delivered_variant->delivered_quantity}}"></td>
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
						<div class="text-right">
							<button type="submit" class="button">Receive items</button>
						</div>
					</div>
				</form>
			@endif
			<!-- Completed deliveries -->
			@if($delivery->delivered_products->where('is_delivered', true)->count() + $delivery->delivered_variants->where('is_delivered', true)->count() > 0)
			<div class="block">
				<h4>Items completely received:</h4>
				<table class="table">
					<thead>
						<tr>
							<th>Product</th>
							<th>Variant</th>
							<th class="text-right">Incoming</th>
							<th class="text-right">Received</th>
						</tr>
					</thead>
					<tbody>
						@foreach($delivery->delivered_products->where('is_delivered', true) as $delivered_product)
							<tr>
								<td>{{$delivered_product->product->name}}</td>
								<td></td>
								<td class="text-right">{{$delivered_product->quantity}}</td>
								<td class="text-right">{{$delivered_product->delivered_quantity}}</td>
							</tr>
						@endforeach
						@foreach($delivery->delivered_variants->where('is_delivered', true) as $delivered_variant)
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
							</tr>
						@endforeach
						<tr style="border-top: 2px solid black">
							<td></td>
							<td class="text-right text-bold">Total:</td>
							<td class="text-right text-bold">{{$delivery->delivered_products->sum('quantity') + $delivery->delivered_variants->sum('quantity')}}</td>
							<td class="text-right text-bold">{{$delivery->delivered_products->sum('delivered_quantity') + $delivery->delivered_variants->sum('delivered_quantity')}}</td>
						</tr>
					</tbody>
				</table>
			</div>
			@endif
		</div>
	</div>
	
	<!-- <script type="text/javascript" src="{{ URL::asset('js/dropzone.min.js') }}"></script> -->
	
@endsection

@section('custom_js')
	<script type="text/javascript" src="{{ URL::asset('js/jquery-ui.min.js') }}"></script> 	
	<script type="text/javascript">
		Dropzone.autoDiscover = false;
	</script>
@endsection