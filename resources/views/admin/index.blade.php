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
	<a href="/{{$company->slug}}" target="_new">Order page</a>
	<div class="row">
		@if(Auth::user()->unreadNotifications()->count() > 0)
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@foreach(Auth::user()->unreadNotifications as $notification)
						<tr>
							<td>{{$notification->data['message']}}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			
		@endif		
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-4 col-md-3">
			<div class="block">
				<h4>Pending orders:</h4>
				<p class="caption">Total:</p>
				<h3>{{$pending_orders->where('date_fulfilled', null)->count()}}</h3>
				<p class="caption">Lag to confirmation:</p>
				<h3>{{round($lead_time_payment[0]->avg)}} days</h3>
			</div>
		</div>
	</div>
@endsection