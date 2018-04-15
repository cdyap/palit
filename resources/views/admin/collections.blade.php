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
	<a href="/collections/new" class="button z-depth-1">Add collection</a>
	<br><br>
	@if($collections->count() > 0)
		<div class="row">
			<div class="col-lg-12">
				@if($available_collections->count() > 0)
				<div class="block table-responsive-sm">
					<h4>Available collections</h4>
						<table class="table">
							<thead>
								<tr>
									<th>Name</th>
									<th>Description</th>
									<th class="text-right" style="max-width:100px;">Inventory</th>
									<th class="text-right" style="max-width:100px;">Products</th>
								</tr>
							</thead>
							<tbody>
								@foreach($available_collections as $collection)
									<tr>
										<td><a href="/collections/{{$collection->slug}}">{{$collection->name}}</a></td>
										<td>{!! nl2br(e($collection->description)) !!}</td>
										<td class="text-right">{{$collection->products->sum('total_inventory')}}</td>
										<td class="text-right">{{$collection->products()->count()}}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
				</div>
				@endif
			</div>

			<div class="col-lg-12">
				@if($unavailable_collections->count() > 0)
				<div class="block table-responsive-sm">
					<h4>Unavailable products</h4>
					<table class="table" style="">
						<thead>
							<tr>
								<th>Name</th>
								<th>Description</th>
								<th class="text-right" >Total quantity</th>
								<th class="text-right">No. of products</th>
							</tr>
						</thead>
						<tbody>
							@foreach($unavailable_collections as $collection)
								<tr>
									<td><a href="/collections/{{$collection->slug}}">{{$collection->name}}</a></td>
									<td>{!! nl2br(e($collection->description)) !!}</td>
									<td class="text-right">{{$collection->products->sum('total_inventory')}}</td>
									<td class="text-right">{{$collection->products()->count()}}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				@endif
			</div>
		</div>
	@else
		<div class="row justify-content-center">
			<div class="empty-state">
				<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" class="mx-auto d-block"
                         viewBox="0 0 200 200" style="enable-background:new 0 0 200 200;" xml:space="preserve">
                    <path d="M168.4,60.1v-5.7c0-9.4-7.7-17.1-17.1-17.1v-5.7c0-9.4-7.7-17.1-17.1-17.1H65.8c-9.4,0-17.1,7.7-17.1,17.1v5.7
                        c-9.4,0-17.1,7.7-17.1,17.1v5.7c-9.4,0-17.1,7.7-17.1,17.1v91.2c0,9.4,7.7,17.1,17.1,17.1h136.9c9.4,0,17.1-7.7,17.1-17.1V77.2
                        C185.5,67.8,177.9,60.1,168.4,60.1z M60.1,31.6c0-3.1,2.6-5.7,5.7-5.7h68.4c3.1,0,5.7,2.6,5.7,5.7v5.7H60.1V31.6z M43,54.4
                        c0-3.1,2.6-5.7,5.7-5.7h102.6c3.1,0,5.7,2.6,5.7,5.7v5.7H43V54.4z M174.1,168.4c0,3.1-2.6,5.7-5.7,5.7H31.6c-3.1,0-5.7-2.6-5.7-5.7
                        V77.2c0-3.1,2.6-5.7,5.7-5.7h136.9c3.1,0,5.7,2.6,5.7,5.7V168.4z"/>
                </svg>
                <h2 class="text-center text-bold">You have no collections!</h2>
			</div>
		</div>
	@endif
@endsection