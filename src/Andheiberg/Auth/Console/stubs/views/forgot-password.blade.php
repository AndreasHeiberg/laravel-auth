@extends('layouts.main')

@section('title')
Login
@stop

@section('main')
	<div class="register-form row">
		<div class="col-md-12">
			+@form()
				<h2>Forgot your password?</h2>
				<p>No problem, we'll send you an email with a password reset.</p>

				+@formText('email')

				<div class="form-actions text-right">
					+@formSubmit('Send')
				</div>
			-@form
		</div>
	</div>
@stop