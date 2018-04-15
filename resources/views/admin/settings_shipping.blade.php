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
	@if ($errors->any())
	    <div class="alert alert-error fade show z-depth-1-half" role="alert">
            @foreach ($errors->all() as $error)
                {{ $error }} <br>
            @endforeach
	    </div>
	@endif
	</div>
	<br>
	<br>
	<h5><a href="/settings">< All settings</a></h5>
	<div class="row">
		<div class="col-xs-12 col-md-8">
			<div class="block">
				<a href="javascript:void(0);" onclick="document.getElementById('add_shipping_block').classList.remove('hide')" class="float-right"  style="margin-left:7px;margin-right:7px;">Add mode of shipping</a>
				<h4>Modes of shipping</h4>
				<table class="table shippings_table">
					<thead>
						<tr>
							<th>Name</th>
							<th>Description</th>
							<th class="text-right">Cost</th>
							<th style="width:20px;"></th>
							<th style="width:30px;"></th>
						</tr>
					</thead>
					<tbody>
						@if($shippings->count()>0)
							@foreach($shippings as $shipping)
								<tr class="shipping_{{$shipping->id}}">
									<td>{{$shipping->name}}</td>
									<td>{{$shipping->description}}</td>
									<td class="text-right">{{$shipping->price}}</td>
									<td class='text-center availability {{(!($shipping->is_available)) ? "text-red" : "text-green"}}' data-toggle="tooltip" data-placement="top" title="{{(!($shipping->is_available)) ? 'Unavailable' : 'Available'}}"><i class="fas fa-circle" ></i></td>
									<td>
										<div class="btn-group dropright float-right">
											<div class="option-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="fas fa-ellipsis-v"></i>
											</div>
											<div class="dropdown-menu">
												<a class="dropdown-item edit_variant" href="javascript:void(0);">Edit mode</a>
												<a class="dropdown-item toggle_availability_shipping" href="javascript:void(0);" data-variant="{{$shipping->id}}">Make {{($shipping->is_available) ? "unavailable" : "available"}}</a>
												<div class="dropdown-divider"></div>
												<a class="dropdown-item delete" data-toggle="modal" data-target="#deleteMode{{$shipping->id}}">Delete mode</a>
											</div>
										</div>
									</td>
								</tr>
							@endforeach
						@else
							<tr class="empty-state">
								<td colspan="3">No modes of shipping created yet. Click <a href="javascript:void(0);" onclick="document.getElementById('add_shipping_block').classList.remove('hide')">here</a> to create one.</td>
							</tr>
						@endif
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-xs-12 col-md-4">
			<div class="block hide" id="add_shipping_block">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById('add_shipping_block').classList.add('hide')">
				<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title" style="margin-bottom:20px;">Add shipping mode:</h5>
				<form action="/settings/shipping/create" method="POST" id="add_shipping_form">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="form-group">
						<label for="inventory">Name:*</label>
						<input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
					</div>			
					<div class="form-group">
						<label for="inventory">Description:</label>
						<textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
					</div>				
					<div class="form-group">
						<label for="currency">Currency:*</label>
						<select name="currency" class="form-control {{ $errors->has('currency') ? 'has-error' : ''}}" required>
							<option selected="selected" disabled>None</option>
							@foreach($currencies as $currency)
								<option value="{{$currency->value_2}}">{{$currency->value}}</option>
							@endforeach						
						</select>
					</div>
					<div class="form-group">
						<label for="price">Price:*</label>
						<input type="number" name="price" class="form-control {{ $errors->has('price') ? 'has-error' : ''}}"  min="0", value="{{ old('price') }}" required>
					</div>
					<p class="note" style="margin-bottom:0;margin-top:10px;">* Required field</p>								
					<br>		
					<div class="form-row">
						<button type="submit" class="button">Add shipping mode</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@section('custom_js')
	@if ($errors->any())
		<script type="text/javascript">
			document.getElementById('add_shipping_block').classList.remove('hide')
		</script>
	@endif
@endsection
