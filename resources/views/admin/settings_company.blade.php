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
	<h5><a href="/settings">< All settings</a></h5>
	<div class="row">
		<div class="col-xs-12 col-md-8">
			<div class="block">
				<h4>Company information:</h4>
				<div class="btn-group dropright float-right">
					<div class="option-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-ellipsis-v"></i>
					</div>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="/settings/company/edit">Edit company</a>
					</div>
				</div>
				<p class="caption">NAME:</p>
				<h5>{{$company->name}}</h5>
			</div>
		</div>
		<div class="col-xs-12 col-md-4">
			
		</div>
	</div>
@endsection