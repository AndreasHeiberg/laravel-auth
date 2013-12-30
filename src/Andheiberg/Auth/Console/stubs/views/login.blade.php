@extends('layouts.main')

@section('title')
Login
@stop

@section('main')
	<div class="login-form row">
		<div class="col-md-6 col-md-push-6">
			+@form(['id' => 'login'])
				<h2>Log in</h2>

				+@formText('email')

				+@formPassword('password')
				
				<div class="form-actions text-right">
					<a href="{{ route('forgot-password') }}" class="btn btn-link">Forgot your password?</a>
					+@formSubmit('Log in')
				</div>
			-@form
		</div>
		<div class="col-md-6 col-md-pull-6 border-right">
			<h2>Get answers to tough questions with mobile surveys.</h2>
			<p>Surveys make it easy for you to ask your customers a question and for them to answer it. Now anyone in your company can create, target, and send beautiful mobile surveys to the people who use your app.</p>

			<div class="form-actions">
				<a href="{{ route('register') }}" class="btn">Register</a>
			</div>
		</div>
	</div>
@stop