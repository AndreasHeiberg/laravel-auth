@extends('layouts.main')

@section('title')
Login
@stop

@section('main')
	<div class="register-form row">
		<div class="col-md-12">
			+@form()
				<h2>Register</h2>

				+@formText('first_name')

				+@formText('last_name')

				+@formText('email')

				+@formPassword('password')

				<small>By signing up for Product, you agree to our <a href="">terms of service</a> and <a href="">privacy policy.</a></small>

				<div class="form-actions text-right">
					+@formSubmit('Register')
				</div>
			-@form
		</div>
	</div>
@stop