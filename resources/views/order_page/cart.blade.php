@extends('layouts.order_page')

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

	<div class="row">
		<h2>CART</h2>
	</div>

	<div class="row">
		<div class="col">
			<table class="table">
				<thead>
					<th>Product</th>
					<th>Variant</th>
					<th class="text-right">Quantity</th>
					<th class="text-right">Total</th>
				</thead>
				<tbody>
					@foreach($cart as $item)
						<?php $i = 0; ?>
						<?php $description = ""; ?>
						@foreach($product_variant_columns->where('name', 'variant_'.$item->options->product->id) as $column)
							<?php $i = $i + 1; ?>
							@if($i != 1)
								<?php $description = $description . ", "; ?>
							@endif
							<?php $description = $description . $column->value . ": " . $item->options->variant->{"attribute_" . $i}; ?>
						@endforeach
						<tr>
							<td>{{$item->name}}</td>
							<td>{{$description}}</td>
							<td class="text-right">{{$item->qty}}</td>
							<td class="text-right">{{$item->options->currency . " " . number_format($item->price, 2, '.', ',')}}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		
	</div>

@endsection