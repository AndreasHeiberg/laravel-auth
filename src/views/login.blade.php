@extends('layouts.main')

@section('title')
Login
@stop

@section('main')
	<div class="container">
		<div class="login-form row">
			<div class="col-md-6 col-md-push-6">
				{{ Form::open(array('route' => 'login')) }}
					{{ Form::token() }}

					<h2>Log in</h2>

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
						<a href="{{ route('password.forgot') }}" class="btn btn-link">Har du glemt din adgangskode?</a>
						<input type="submit" class="btn btn-primary" value="Log in">
					</div>
				{{ Form::close() }}
			</div>
			<div class="col-md-6 col-md-pull-6 border-right">
				<h2>Bliv en del af familien</h2>
				<p>Det tager fem minutter at lave en bruger.</p>

				<div class="form-actions">
					<a href="{{ route('register') }}" class="btn">Opret en bruger</a>
				</div>
			</div>
		</div>
	</div>
@stop