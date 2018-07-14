@extends('layouts.app')

@section('content')
	<div class="row">
		<div class="alerts-holder">
			@if(session('error'))
			    <div class="alert alert-error fade alert-dismissible show z-depth-1-half" role="alert" data-auto-dismiss>
		            {{session('error')}}
		            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
			    </div>
			@endif
			@if ($errors->any())
			    <div class="alert alert-error fade alert-dismissible show z-depth-1-half" role="alert">
		            @foreach ($errors->all() as $error)
		                {{ $error }} <br>
		            @endforeach
		            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
			    </div>
			@endif
		</div>
		<div class="col d-flex justify-content-center align-items-center">
			<div class="form">
				<form action="/view-order" method="POST" class="">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<h1>How's your order?</h1>
					<br>
					<div class="form-group">
						<label for="name">Email:</label>
						<input type="email" name="email" class="form-control {{ $errors->has('email') ? 'has-error' : ''}}" required value="{{ old('email') }}">
					</div>
					<div class="form-group">
						<label for="name">Hash:</label>
						<input type="text" name="hash" class="form-control {{ $errors->has('hash') ? 'has-error' : ''}}" required value="{{ old('hash') }}">
					</div>
					<br><br>
					<button class="button">Check status</button>
				</form>
			</div>
		</div>
	</div>
@endsection
