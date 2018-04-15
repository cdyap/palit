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
	<h5><a href="/products">< All products</a></h5>
	<div class="row">
		<div class="col-xs-12 col-lg-9">
			<div class="block">
				<h4>Product information for <b>{{$product->name}}</b>:</h4>
				<div class="btn-group dropright float-right">
					<div class="option-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-ellipsis-v"></i>
					</div>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="/products/{{$product->slug}}/edit">Edit product</a>
						<a class="dropdown-item" href="javascript:void(0);" id="toggle_availability" variant="{{$product->id}}">Make {{($product->is_available) ? "unavailable" : "available"}}</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#uploadImage">Upload header image</a>
						<a class="dropdown-item delete-header-image-option {{ (empty($product->image_url)) ? 'hide' : '' }}" href="javascript:void(0);" data-toggle="modal" data-target="#deleteImage">Delete header image</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item delete" data-toggle="modal" data-target="#deleteProduct">Delete product</a>
					</div>
				</div>
				<p class="caption">STATUS:</p>
				@if (!$product->is_available)
					<h5 class="text-red" id="product_availability">UNAVAILABLE</h5>
				@else
					<h5 class="" id="product_availability">AVAILABLE</h5>
				@endif
				
				<p class="caption" id="anchor_for_new_variant">DESCRIPTION:</p>
				<h5>{!! nl2br(e($product->description)) !!}</h5>
				
				<p class="caption">PRICE:</p>
				<h5>{{ $product->hasSameVariantPrices() ? $product->view_price() : $product->variants->sortBy('price')->pluck('view_price')->unique()->implode(', ') }}</h5>

				@if(!empty($product->SKU))
					<p class="caption">SKU:</p>
					<h5>{{ $product->SKU }}</h5>
				@endif

				<p class="caption">TOTAL QUANTITY:</p>
				<h5 id="total_inventory">{{ (empty($product->total_inventory)) ? 0 : $product->total_inventory }}</h5>
				
			</div>
		</div>
		<div class="col-xs-12 col-lg-3">
			<div class="block header-image-block {{ (empty($product->image_url)) ? 'hide' : '' }}">
				<h4>Header image:</h4>
				<div class="square-container">
					<img src="/uploads/{{$product->image_url}}" alt="Header image" class="img-responsive">
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-12 col-lg-9">
			<div class="block">
				<h4>Shipping and selling:</h4>
				<p class="caption">ALLOW ORDERS TO EXCEED CURRENT INVENTORY?</p>
				@if (!$product->overselling_allowed)
					<h5>DO NOT ALLOW</h5>
				@else
					<h5>ALLOW</h5>
				@endif
				<p class="caption">SHIPPING REQUIRED?</p>
				@if (!$product->is_shipped)
					<h5>NOT REQUIRED</h5>
				@else
					<h5>REQUIRED</h5>
				@endif
				<p class="caption">ITEMS PER SHIPMENT:</p>
				<h5>{{$product->item_per_shipment}}</h5>
			</div>
		</div>
	</div>

	<div class="row" >
		@if($product->variant_columns()->count() > 0) 
			<div class="col-xs-12 col-lg-9">
				<div class="block table-responsive-sm">
					
					<a href="#" data-toggle="modal" data-target="#addVariantTypes" class="modal-launcher-link float-right" style="margin-left:7px;margin-right:5px;">Edit columns</a>
					<a href="javascript:void(0);" onclick="document.getElementById('add_variant_block').classList.remove('hide')" class="float-right"  style="margin-left:7px;margin-right:7px;">Add variants</a>
					<h4 style="">Variants:</h4>
					<p class="note">Reorder variants by clicking and dragging the handle icon.</p>
					<table class="table variants_table table-hover">
						<thead>
							<tr>
								<th style="width:10px;"></th>
								@foreach($product->variant_columns()->sortBy('value_2') as $column)
									<th>{{$column->value}}</th>
								@endforeach
								<th class="text-right">Quantity</th>
								<th class="text-right">Price</th>
								<th style="width:20px;"></th>
								<th style="width:30px;"></th>
							</tr>
						</thead>
						<tbody data-entityname="variants">
							@if($product->variants->count() == 0)
								<tr id="no_variants_yet">
									<td colspan="{{$product->variant_columns()->count() + 5}}">No variants yet. Click <a href="javascript:void(0);" onclick="document.getElementById('add_variant_block').classList.remove('hide')" >here</a> to add one.</td>
								</tr>
							@else
								@foreach($product->variants->sortBy('position') as $variant)
									<tr class="variant_{{$variant->id}}" data-id="{{$variant->id}}" data-inventory="{{$variant->inventory}}" data-price="{{$variant->price}}" data-itemId="{{{ $variant->id }}}">
										<td class="sortable-handle"><i class="fas fa-arrows-alt-v"></i></td>
										@foreach($product->variant_columns()->sortBy('value_2') as $column)
											<td class="{{$column->value_2}}">{{ $variant->{$column->value_2} }}</td>
										@endforeach
										
										<td class="text-right">{{$variant->inventory}}</td>
										<td class="text-right">{{$variant->view_price()}}</td>
										<td class='text-center availability {{(!($variant->is_available)) ? "text-red" : "text-green"}}' data-toggle="tooltip" data-placement="top" title="{{(!($variant->is_available)) ? 'Unavailable' : 'Available'}}"><i class="fas fa-circle" ></i></td>
										<td>
											<div class="btn-group dropright float-right">
												<div class="option-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</div>
												<div class="dropdown-menu">
													<a class="dropdown-item edit_variant" href="javascript:void(0);">Edit variant</a>
													<a class="dropdown-item toggle_availability_variant" href="javascript:void(0);" data-variant="{{$variant->id}}">Make {{($variant->is_available) ? "unavailable" : "available"}}</a>
													<div class="dropdown-divider"></div>
													<a class="dropdown-item delete" data-toggle="modal" data-target="#deleteVariant{{$variant->id}}">Delete variant</a>
												</div>
											</div>
										</td>
									
										<div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="deleteVariant{{$variant->id}}" aria-labelledby="deleteVariant" aria-hidden="true">
											<div class="modal-dialog modal modal-dialog-centered" role="document">
												<div class="modal-content ">
													<form action="/products/{{$product->slug}}/{{$variant->id}}/delete" method="POST" class="delete_variant_form" row_class="variant_{{$variant->id}}">
														<input type="hidden" name="_token" value="{{ csrf_token() }}">
														<input type="hidden" name="_method" value="delete" />
														<div class="modal-header">
															<h5 class="modal-title" id="exampleModalLabel">Delete this variant?</h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
															</button>
														</div>
															<div class="modal-body">
																<h5>Are you sure you want to delete this variant? This cannot be undone.</h5>
																@if(isset($variant->attribute_1))
																	<p class="caption">{{ $product->variant_columns()->where('value_2', 'attribute_1')->first()->value }}</p>
																	<h5>{{$variant->attribute_1}}</h5>
																@endif
																@if(isset($variant->attribute_2))
																	<p class="caption">{{ $product->variant_columns()->where('value_2', 'attribute_2')->first()->value }}</p>
																	<h5>{{$variant->attribute_2}}</h5>
																@endif
																@if(isset($variant->attribute_3))
																	<p class="caption">{{ $product->variant_columns()->where('value_2', 'attribute_3')->first()->value }}</p>
																	<h5>{{$variant->attribute_3}}</h5>
																@endif
																@if(isset($variant->attribute_4))
																	<p class="caption">{{ $product->variant_columns()->where('value_2', 'attribute_4')->first()->value }}</p>
																	<h5>{{$variant->attribute_4}}</h5>
																@endif
																@if(isset($variant->attribute_5))
																	<p class="caption">{{ $product->variant_columns()->where('value_2', 'attribute_5')->first()->value }}</p>
																	<h5>{{$variant->attribute_5}}</h5>
																@endif
															</div>
														<div class="modal-footer">
															<button type="button" class="button secondary" data-dismiss="modal">Cancel</button>
															<button type="submit" class="button delete">Delete</button>
														</div>
													</form>
												</div>
											</div>
										</div>
									</tr>
								@endforeach
							@endif
						</tbody>
					</table>
				</div>	
				<div class="modal fade bd-example-modal-lg" id="addVariantTypes" tabindex="-1" role="dialog" aria-labelledby="Add variant types" aria-hidden="true">
					<div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
							<form action="/products/{{$product->slug}}/addVariantColumn" method="POST" class="with-cascading-disabling">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<div class="modal-header">
									<h5 class="modal-title" id="exampleModalLabel">Edit variant columns</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
									<div class="modal-body">
										<p class="note">Enter new columns consecutively.<br>Delete a column by leaving the textbox empty. This will erase all variant data of that column.<br>Deleting all columns will also erase all variants.</p>
										<div class="form-row">
											<div class="col-md">
												<label for="attribute_1">Column 1:</label>
												<input type="text" name="attribute_1" class="form-control {{ $errors->has('attribute_1') ? 'has-error' : ''}}" value="{{ $product->variant_columns()->where('value_2', 'attribute_1')->count() == 1 ? $product->variant_columns()->where('value_2', 'attribute_1')->first()->value : "" }}">
											</div>
											<div class="col-md">
												<label for="attribute_2">Column 2:</label>
												<input type="text" name="attribute_2" class="form-control {{ $errors->has('attribute_2') ? 'has-error' : ''}}" value="{{ $product->variant_columns()->where('value_2', 'attribute_2')->count() == 1 ? $product->variant_columns()->where('value_2', 'attribute_2')->first()->value : "" }}" {{ $product->variant_columns()->where('value_2', 'attribute_2')->count() == 1 || $product->variant_columns()->where('value_2', 'attribute_1')->count() == 1 ? "" : "disabled" }}>
											</div>
											<div class="col-md">
												<label for="attribute_3">Column 3:</label>
												<input type="text" name="attribute_3" class="form-control {{ $errors->has('attribute_3') ? 'has-error' : ''}}" value="{{ $product->variant_columns()->where('value_2', 'attribute_3')->count() == 1 ? $product->variant_columns()->where('value_2', 'attribute_3')->first()->value : "" }}" {{ $product->variant_columns()->where('value_2', 'attribute_3')->count() == 1 || $product->variant_columns()->where('value_2', 'attribute_2')->count() == 1 ? "" : "disabled" }}>
											</div>
											<div class="col-md">
												<label for="attribute_4">Column 4:</label>
												<input type="text" name="attribute_4" class="form-control {{ $errors->has('attribute_4') ? 'has-error' : ''}}" value="{{ $product->variant_columns()->where('value_2', 'attribute_4')->count() == 1 ? $product->variant_columns()->where('value_2', 'attribute_4')->first()->value : "" }}" {{ $product->variant_columns()->where('value_2', 'attribute_4')->count() == 1 || $product->variant_columns()->where('value_2', 'attribute_3')->count() == 1 ? "" : "disabled" }}>
											</div>
											<div class="col-md">
												<label for="attribute_5">Column 5:</label>
												<input type="text" name="attribute_5" class="form-control {{ $errors->has('attribute_5') ? 'has-error' : ''}}" value="{{ $product->variant_columns()->where('value_2', 'attribute_5')->count() == 1 ? $product->variant_columns()->where('value_2', 'attribute_5')->first()->value : "" }}" {{ $product->variant_columns()->where('value_2', 'attribute_5')->count() == 1 || $product->variant_columns()->where('value_2', 'attribute_4')->count() == 1 ? "" : "disabled" }}>
											</div>
										</div>
									</div>
								<div class="modal-footer">
									<button type="button" class="button btn-secondary" data-dismiss="modal">Close</button>
									<button type="submit" class="button">Edit columns</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-lg-3">
				<div class="block hide" id="add_variant_block">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById('add_variant_block').classList.add('hide')">
					<span aria-hidden="true">&times;</span>
					</button>
					<h5 class="modal-title" style="margin-bottom:20px;">Add variants:</h5>
					<form action="/products/{{$product->slug}}/addVariant" method="POST" id="add_variant_form">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						@for ($i = 1; $i <= 5; $i++)
							@if($product->variant_columns()->where('value_2', 'attribute_'.$i)->count() == 1)
								<div class="form-group">
									<label for="attribute_{{$i}}">{{$product->variant_columns()->where('value_2', 'attribute_'.$i)->first()->value}}:</label>
									<input type="text" name="attribute_{{$i}}" class="form-control" value="">
								</div>
							@endif
						@endfor		
						<div class="form-group">
							<label for="inventory">Quantity:</label>
							<input type="number" name="inventory" class="form-control" value="0" min="0" required>
						</div>				
						<div class="form-group">
							<label for="price">Price:</label>
							<input type="number" name="price" class="form-control" value="{{$product->price}}" min="0" required>
						</div>					
						<br>		
						<div class="form-row">
							<button type="submit" class="button">Add variant</button>
						</div>
					</form>
				</div>
				<div class="block hide" id="edit_variant_block" style="margin-top:0px;">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
					<h5 class="modal-title" style="margin-bottom:20px;">Edit variants:</h5>
					<form action="/products/{{$product->slug}}/editVariant" method="POST" id="add_variant_form">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="_method" value="PATCH">
						@for ($i = 1; $i <= 5; $i++)
							@if($product->variant_columns()->where('value_2', 'attribute_'.$i)->count() == 1)
								<div class="form-group">
									<label for="attribute_{{$i}}">{{$product->variant_columns()->where('value_2', 'attribute_'.$i)->first()->value}}:</label>
									<input type="text" name="attribute_{{$i}}" class="form-control" value="">
								</div>
							@endif
						@endfor		
						<div class="form-group">
							<label for="inventory">Quantity:</label>
							<input type="number" name="inventory" class="form-control" value="" min="0" required>
						</div>				
						<div class="form-group">
							<label for="price">Price:</label>
							<input type="number" name="price" class="form-control" value="{{$product->price}}" min="0" required>
						</div>					
						<br>		
						<input type="hidden" name="variant_id">
						<div class="form-row">
							<button type="submit" class="button">Edit variant</button>
						</div>
					</form>
				</div>
			</div>
		@else
			<div class="col-lg-9 col-xs-12">
				<p>Click <a href="" data-toggle="modal" data-target="#addVariantTypes" class="modal-launcher-link">here</a> to add variant columns.</p>
				<div class="modal fade bd-example-modal-lg" id="addVariantTypes" tabindex="-1" role="dialog" aria-labelledby="AddVariantTypes" aria-hidden="true">
					<div class="modal-dialog modal-lg" role="document">
						<div class="modal-content ">
							<form action="/products/{{$product->slug}}/addVariantColumn" method="POST" class="with-cascading-disabling">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<div class="modal-header">
									<h5 class="modal-title" id="exampleModalLabel">Add variant columns</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
									<div class="modal-body">
										<p class="note">Enter new columns consecutively.</p>
										<div class="form-row">
											<div class="col-md">
												<label for="attribute_1">Column 1:</label>
												<input type="text" name="attribute_1" class="form-control {{ $errors->has('attribute_1') ? 'has-error' : ''}}" >
											</div>
											<div class="col-md">
												<label for="attribute_2">Column 2:</label>
												<input type="text" name="attribute_2" class="form-control {{ $errors->has('attribute_2') ? 'has-error' : ''}}" disabled>
											</div>
											<div class="col-md">
												<label for="attribute_3">Column 3:</label>
												<input type="text" name="attribute_3" class="form-control {{ $errors->has('attribute_3') ? 'has-error' : ''}}" disabled>
											</div>
											<div class="col-md">
												<label for="attribute_4">Column 4:</label>
												<input type="text" name="attribute_4" class="form-control {{ $errors->has('attribute_4') ? 'has-error' : ''}}" disabled>
											</div>
											<div class="col-md">
												<label for="attribute_5">Column 5:</label>
												<input type="text" name="attribute_5" class="form-control {{ $errors->has('attribute_5') ? 'has-error' : ''}}" disabled>
											</div>
										</div>
									</div>
								<div class="modal-footer">
									<button type="button" class="button btn-secondary" data-dismiss="modal">Close</button>
									<button type="submit" class="button btn-primary">Add columns</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		@endif
		
		<!-- delete product modal -->
		<div class="modal fade bd-example-modal" id="deleteProduct" tabindex="-1" role="dialog" aria-labelledby="deleteProduct" aria-hidden="true">
			<div class="modal-dialog modal modal-dialog-centered" role="document">
				<div class="modal-content ">
					<form action="/products/{{$product->slug}}/delete" method="POST" >
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="_method" value="delete" />
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">Delete {{$product->name}}?</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
							<div class="modal-body">
								<h5>Are you sure you want to delete <b>{{$product->name}}</b>? All variants will also be deleted. This cannot be undone.</h5>
							</div>
						<div class="modal-footer">
							<button type="button" class="button secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" class="button delete">Delete</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- remove header image modal -->
		<div class="modal fade bd-example-modal" id="deleteImage" tabindex="-1" role="dialog" aria-labelledby="deleteImage" aria-hidden="true">
			<div class="modal-dialog modal modal-dialog-centered" role="document">
				<div class="modal-content ">
					<form action="/products/{{$product->slug}}/deleteHeaderImage" method="POST" >
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="_method" value="delete" />
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">DELETE HEADER IMAGE</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
							<div class="modal-body">
								<h5>Delete header image for {{$product->name}}?</h5>
							</div>
						<div class="modal-footer">
							<button type="button" class="button secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" class="button delete">Delete</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- upload image modal -->
		<div class="modal fade bd-example-modal-lg" id="uploadImage" tabindex="-1" role="dialog" aria-labelledby="uploadImage" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content ">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Upload header image for {{$product->name}}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
						<div class="modal-body">
							
							<form action="/products/{{$product->slug}}/uploadHeaderImage" method="POST" enctype="multipart/form-data" class="dropzone .align-items-center .justify-content-center" id="upload-image-dropzone">
							    {{ csrf_field() }}
							</form>
							<br>
							<p class="note">Only <b>one image file (.png, .jpg, .gif)</b> with maximum dimensions of <b>1000 x 1000 pixels</b> and a size of <b>2 MB</b> will be accepted.</p>
						</div>
					<div class="modal-footer hide">
						<button type="button" class="button secondary" data-dismiss="modal">Cancel</button>
						<button type="submit" class="button accent">Upload</button>
					</div>
				</div>
			</div>
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

@section('fontawesome')
	 <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
@endsection