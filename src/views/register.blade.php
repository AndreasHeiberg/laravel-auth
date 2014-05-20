@extends('layouts.main')

@section('title')
Opret bruger - Momentum, det bedste sted at at komme igang med dit it-projekt.
@stop

@section('main')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				{{ Form::open(array('route' => 'register')) }}
					{{ Form::token() }}
					
					<h2>Opret en bruger</h2>

					<div class="form-group {{ $errors->has('first_name') ? 'has-error' : ''}}">
						<label for="first_name" class="control-label">First name</label>
						<div class="form-controls">
							<input type="text" id="first_name" name="first_name" value="{{ Input::old('first_name') }}" class="form-control" placeholder="e.g. John">
						</div>
						@foreach($errors->get('first_name') as $message)
							<span class='help-block'>{{ $message }}</span>
						@endforeach
					</div>

					<div class="form-group {{ $errors->has('last_name') ? 'has-error' : ''}}">
						<label for="last_name" class="control-label">Last name</label>
						<div class="form-controls">
							<input type="text" id="last_name" name="last_name" value="{{ Input::old('last_name') }}" class="form-control" placeholder="e.g. Joe">
						</div>
						@foreach($errors->get('last_name') as $message)
							<span class='help-block'>{{ $message }}</span>
						@endforeach
					</div>

					<div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
						<label for="email" class="control-label">Email</label>
						<div class="form-controls">
							<input type="text" id="email" name="email" value="{{ Input::old('email') }}" class="form-control" placeholder="e.g. john.joe@gmail.com">
						</div>
						@foreach($errors->get('email') as $message)
							<span class='help-block'>{{ $message }}</span>
						@endforeach
					</div>

					<div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
						<label for="password" class="control-label">Password</label>
						<div class="form-controls">
							<input type="password" id="password" name="password" value="{{ Input::old('password') }}" class="form-control" placeholder="e.g. •••••">
						</div>
						@foreach($errors->get('password') as $message)
							<span class='help-block'>{{ $message }}</span>
						@endforeach
					</div>

					<div class="form-actions text-right">
						<input type="submit" class="btn btn-primary" value="Register">
					</div>

				{{ Form::close() }}
			</div>
		</div>
	</div>
@stop