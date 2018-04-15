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
	<div class="col-xs-12 col-md-4">
		<table class="table settings-table">
			<tbody>
				<tr><td><a href="/settings/company">Company</a></td></tr>
				<tr><td><a href="/settings/shipping">Shipping</a></td></tr>
				<tr><td><a href="/settings/payment_methods">Payment methods</a></td></tr>
			</tbody>
		</table>
	</div>
	<div class="col-xs-12 col-md-8">
		
	</div>
@endsection