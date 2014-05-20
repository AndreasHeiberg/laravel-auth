@extends('layouts.main')

@section('title')
Login
@stop

@section('main')
	<div class="container">
		<div class="register-form row">
			<div class="col-md-12">
				{{ Form::open(array('route' => 'password.reset')) }}
					{{ Form::token() }}

					<h2>Reset your password</h2>
					
					<input type="hidden" name="token" value="{{ $token }}">

					<input type="hidden" name="email" value="{{ $email }}">

					<div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
						<label for="password" class="control-label">New Password</label>
						<div class="form-controls">
							<input type="password" id="password" name="password" class="form-control" placeholder="e.g. •••••">
						</div>
						@foreach($errors->get('password') as $message)
							<span class='help-block'>{{ $message }}</span>
						@endforeach
					</div>

					<div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : ''}}">
						<label for="password_confirmation" class="control-label">Confirm Password</label>
						<div class="form-controls">
							<input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="e.g. •••••">
						</div>
						@foreach($errors->get('password_confirmation') as $message)
							<span class='help-block'>{{ $message }}</span>
						@endforeach
					</div>

					<div class="form-actions text-right">
						<input type="submit" value="Reset password" class="btn btn-primary">
					</div>

				{{ Form::close() }}
			</div>
		</div>
	</div>
@stop