@extends('layouts.main')

@section('title')
Resend verification email
@stop

@section('main')
	<div class="container">
		<div class="register-form row">
			<div class="col-md-12">
				{{ Form::open(array('route' => 'verification.resend')) }}
					{{ Form::token() }}

					<h2>Har du glemt dit password?</h2>
					<p>Det går nok, vi sender dig en email med et password reset link.</p>

					<div class="form-group ">
						<label for="email" class="control-label">Email </label>
						<div class="form-controls">
							<input type="text" id="email" name="email" value="{{ Input::old('email') }}" class="form-control" placeholder="">
						</div>
					</div>

					<div class="form-actions text-right">
						<input type="submit" value="Send">
					</div>

				{{ Form::close() }}
			</div>
		</div>
	</div>
@stop