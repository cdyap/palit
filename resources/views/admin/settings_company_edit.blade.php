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
	<h5><a href="/settings/company">< Company information</a></h5>
	<div class="row">
		<div class="col-xs-12 col-md-8">
			<div class="block">
				<h4>Edit company information:</h4>
				<form action="/settings/company/update" method="POST">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="_method" value="PATCH">
					<div class="form-group">
						<label for="name">Name:*</label>
						<input type="text" name="name" class="form-control {{ $errors->has('name') ? 'has-error' : ''}}" required value="{{ $company->name }}">
					</div>
					<div class="form-group">
						<label for="name">Currency:*</label>
						<select name="currency" class="form-control {{$errors->has('currency') ? 'has-error' : ''}}" required>
							@foreach($currencies as $currency)
								<option value="{{$currency->value_2}},{{$currency->value}}" {{($currency->value_2.$currency->value == $company->currency.$company->currency_name) ? "selected" : ""}}>{{$currency->value . " (" . $currency->value_2 .")"}}</option>
							@endforeach
						</select>
					</div>
					<p class="note" style="margin-bottom:0;margin-top:10px;">* Required field</p>	
					<br>
					<button type="submit" class="button">Save company details</button>
				</form>
			</div>
		</div>
	</div>
@endsection