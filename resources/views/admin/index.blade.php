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
	<div class="row">
		<form action="/orders/toggleAutoConfirm" method="POST" id="toggleAutoConfirmForm">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="_method" value="PATCH">
			<div class="auto-confirm">
				<label class="switch">
				<input type="checkbox" {{($company->auto_confirm_orders) ? "checked" : ""}} value="true" name="auto_confirm_orders">
				<span class="slider"></span>
				</label>
				<p>Auto-confirm orders</p>
			</div>
		</form>		
	</div>
@endsection