@extends('layouts.admin')

@section('content')
	<br>
	<h5><a href="/collections">< All collections</a></h5>
	<div class="row">
		<div class="col-lg-8">
			<form action="/collections/store" method="POST">
				<div class="block">
					<h4>Collection information:</h4>
					@if ($errors->any())
					    <div class="alert alert-error fade show z-depth-1-half" role="alert">
				            @foreach ($errors->all() as $error)
				                {{ $error }} <br>
				            @endforeach
					    </div>
					@endif
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="form-group">
						<label for="name">Name:*</label>
						<input type="text" name="name" class="form-control {{ $errors->has('name') ? 'has-error' : ''}}" required value="{{ old('name') }}">
					</div>
					<div class="form-group">
						<label for="description">Description:*</label>
						<textarea name="description" required class="form-control {{ $errors->has('description') ? 'has-error' : ''}}" rows="4">{{ old('description') }}</textarea>
					</div>
					<p class="note" style="margin-bottom:0;margin-top:10px;">* Required field</p>			
				</div>
				
				<br>
				<button type="submit" class="button z-depth-1">Save collection</button>
			</form>
		</div>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js">
@endsection