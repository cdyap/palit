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
	<h5><a href="/collections">< All collections</a></h5>
	<div class="row">
		<div class="col-xs-12 col-lg-9">
			<div class="block">
				<h4>Collection information for <b>{{$collection->name}}</b>:</h4>
				<div class="btn-group dropright float-right">
					<div class="option-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-ellipsis-v"></i>
					</div>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="/collections/{{$collection->slug}}/edit">Edit collection</a>
						<a class="dropdown-item" href="javascript:void(0);" id="toggle_availability_collection" collection="{{$collection->id}}">Make {{($collection->is_available) ? "unavailable" : "available"}}</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#uploadImage">Upload header image</a>
						<a class="dropdown-item delete-header-image-option {{ (empty($collection->image_url)) ? 'hide' : '' }}" href="javascript:void(0);" data-toggle="modal" data-target="#deleteImage">Delete header image</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item delete" data-toggle="modal" data-target="#deleteCollection">Delete collection</a>
					</div>
				</div>
				<p class="caption">STATUS:</p>
				@if (!$collection->is_available)
					<h5 class="text-red" id="product_availability">UNAVAILABLE</h5>
				@else
					<h5 class="" id="product_availability">AVAILABLE</h5>
				@endif
				
				<p class="caption" id="anchor_for_new_variant">DESCRIPTION:</p>
				<h5>{!! nl2br(e($collection->description)) !!}</h5>
				
				<p class="caption">TOTAL QUANTITY:</p>
				<h5 id="total_inventory">{{$collection->products->sum('total_inventory')}}</h5>
			</div>
		</div>
		<div class="col-xs-12 col-lg-3">
			<div class="block header-image-block {{ (empty($collection->image_url)) ? 'hide' : '' }}">
				<h4>Header image:</h4>
				<div class="square-container">
					<img src="/uploads/{{$collection->image_url}}" alt="Header image" class="img-responsive">
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-lg-9">
			<div class="block table-responsive-sm">
				<h4>Products:</h4>
				@if($collection->products->count() == 0)
					<p>No products yet. Click <a href="javascript:void(0);" class="add_product_block" data-collectionslug="{{$collection->slug}}">here</a> to add.</p>
				@else
					<table class="table products_table table-hover">
						<a href="javascript:void(0);" class="add_product_block float-right" style="margin-right:7px;" data-collectionslug="{{$collection->slug}}">Add products</a>
						<thead>
							<tr>
								<th>Name:</th>
								<th class="text-right">Quantity:</th>
								<th class="text-right">Variants:</th>
								<th class="text-right">Price:</th>
								<th style="width:20px;"></th>
								<th style="width:30px;"></th>
							</tr>
						</thead>
						<tbody>
							@foreach($collection->products as $product)
								<tr class="{{$product->slug}}">
									<td><a href="/products/{{$product->slug}}">{{$product->name}}</a></td>
									<td class="text-right">{{$product->total_inventory}}</td>
									<td class="text-right">{{$product->variants()->count()}}</td>
									<td class="text-right">{{ $product->hasSameVariantPrices() ? $product->view_price() : $product->variants->sortBy('price')->pluck('view_price')->unique()->implode(', ') }}</td>
									<td class='text-center availability {{(!($product->is_available)) ? "text-red" : "text-green"}}' data-toggle="tooltip" data-placement="top" title="{{(!($product->is_available)) ? 'Unavailable' : 'Available'}}"><i class="fas fa-circle" ></i></td>
									<td>
										<div class="btn-group dropright float-right">
											<div class="option-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="fas fa-ellipsis-v"></i>
											</div>
											<div class="dropdown-menu">
												<a class="dropdown-item toggle_availability_product" href="javascript:void(0);" data-product="{{$product->id}}">Make {{($product->is_available) ? "unavailable" : "available"}}</a>
												<div class="dropdown-divider"></div>
												<a class="dropdown-item remove" href="javascript:void(0);" data-product="{{$product->slug}}" data-collection="{{$collection->slug}}">Remove from collection</a>
											</div>
										</div>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				@endif
			</div>
		</div>
		<div class="col-xs-12 col-lg-3">
			<div class="block hide" id="add_product_block">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById('add_product_block').classList.add('hide')">
				<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title" style="margin-bottom:20px;">Add products:</h5>
				<form action="/collections/{{$collection->slug}}/addProducts" method="POST" id="add_product_form">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<table class="table">
						<thead>
							<tr>
								<th></th>
								<th>Name</th>
							</tr>
						</thead>
						<tbody>
							@foreach($products->sortBy('name') as $product)
								<tr>
									<td>
										<div class="form-check">
											<input class="form-check-input" type="checkbox" name="products[]" value="{{$product->id}}">
										</div>
									</td>
									<td>{{$product->name}}</td>
								</tr>
							@endforeach
						</tbody>
					</table>					
					<br>		
					<div class="form-row">
						<button type="submit" class="button">Add products</button>
					</div>
				</form>
			</div>
		</div>
		<div class="modal fade bd-example-modal" id="deleteCollection" tabindex="-1" role="dialog" aria-labelledby="deleteCollection" aria-hidden="true">
			<div class="modal-dialog modal modal-dialog-centered" role="document">
				<div class="modal-content ">
					<form action="/collections/{{$collection->slug}}/delete" method="POST" >
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="_method" value="delete" />
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">Delete {{$collection->name}}?</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
							<div class="modal-body">
								<h5>Are you sure you want to delete <b>{{$collection->name}}</b>? This cannot be undone.</h5>
							</div>
						<div class="modal-footer">
							<button type="button" class="button secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" class="button delete">Delete</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- remove header image modal -->
	<div class="modal fade bd-example-modal" id="deleteImage" tabindex="-1" role="dialog" aria-labelledby="deleteImage" aria-hidden="true">
		<div class="modal-dialog modal modal-dialog-centered" role="document">
			<div class="modal-content ">
				<form action="/collections/{{$collection->slug}}/deleteHeaderImage" method="POST" >
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="_method" value="delete" />
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">DELETE HEADER IMAGE</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
						<div class="modal-body">
							<h5>Delete header image for {{$collection->name}}?</h5>
						</div>
					<div class="modal-footer">
						<button type="button" class="button secondary" data-dismiss="modal">Cancel</button>
						<button type="submit" class="button delete">Delete</button>
					</div>
				</form>
			</div>
		</div>
	</div>


	<div class="modal fade bd-example-modal-lg" id="uploadImage" tabindex="-1" role="dialog" aria-labelledby="uploadImage" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content ">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Upload header image for {{$collection->name}}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
						<div class="modal-body">
							<form action="/collections/{{$collection->slug}}/uploadHeaderImage" method="POST" enctype="multipart/form-data" class="dropzone .align-items-center .justify-content-center" id="upload-image-dropzone">
							    {{ csrf_field() }}
							</form>
							<br>
							<p class="note">Only <b>one image file</b> with maximum dimensions of <b>1000 x 1000 pixels</b> and a size of <b>2 MB</b> will be accepted.</p>
						</div>
					<div class="modal-footer">
						<button type="button" class="button secondary" data-dismiss="modal">Cancel</button>
						<button type="submit" class="button delete">Delete</button>
					</div>
				</div>
			</div>
		</div>
		
	</div>
	<script type="text/javascript" src="{{ URL::asset('js/dropzone.min.js') }}"></script>
	<script type="text/javascript">
		Dropzone.autoDiscover = false;
	</script>
@endsection

@section('custom_js')
@endsection