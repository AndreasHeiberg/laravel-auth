<?php

use Models\User;

class AuthController extends BaseController {

	public function getLogin()
	{
		if ( ! Auth::guest())
		{
			Notify::info("You are already logged in.")->flash();

			return Redirect::route('dashboard');
		}

		return View::make('auth.login');
	}

	public function postLogin()
	{
		$email = Input::get('email');
		$password = Input::get('password');

		$auth = Auth::attempt(compact('email', 'password'), true);

		if ($auth->hasErrors())
		{
			return Redirect::back()->withInput()->withErrors( $auth->errors );
		}

		$user = Auth::user();

		Notify::info("Welcome, {$user->first_name}!")->flash();

		return Redirect::intended(route('dashboard'));
	}

	public function getLogout()
	{
		Auth::logout();

		Notify::success('Thanks for using [product] it means a lot to us. See you next time.')->flash();
		
		return Redirect::route('login');
	}

	/**
	 * Show the form for creating a new user.
	 *
	 * @return Response
	 */
	public function getRegister()
	{
		if ( ! Auth::guest())
		{
			Notify::info("You can't register as you already have an account.")->flash();

			return Redirect::route('dashboard');
		}

		return View::make('auth.register');
	}

	/**
	 * Store a newly created user in storage.
	 *
	 * @return Response
	 */
	public function postRegister()
	{
		$credentials = Input::except('_token');
		
		$auth = Auth::register($credentials);

		if ($auth->hasErrors())
		{
			return Redirect::back()->withInput()->withErrors($auth->errors);
		}

		Notify::success("A verification email has been sent to {$credentials['email']}. <b>Please check your inbox and your spam filter.</b>")->flash();
		
		return Redirect::intended(route('login'));
	}

	/**
	 * Show the form for creating a new user.
	 *
	 * @return Response
	 */
	public function getVerifyEmail()
	{
		$credentials = Input::only('email', 'token');

		$auth = Auth::verifyEmail($credentials);

		if ($auth->hasErrors())
		{
			dd($auth->errors);
			return Redirect::route('forgot-password')->hasErrors($auth->errors);
		}

		Notify::success('Your email has been verified.')->flash();

		Auth::loginUsingEmail($credentials['email']);

		return Redirect::route('dashboard');
	}

	public function getForgotPassword()
	{
		return View::make('auth.forgot-password');
	}

	public function postForgotPassword()
	{
		$reminder = Password::remind(['email' => Input::get('email')]);
		
		if ($reminder->hasErrors())
		{
			return Redirect::back()->withErrors($reminder->errors);
		}

		Notify::success("We have sent you an email with further information on how to reset your password.")->flash();

		return Redirect::route('login');
	}

	public function getResetPassword()
	{
		$email = Input::get('email');
		$token = Input::get('token');

		return View::make('auth.reset-password', compact('email', 'token'));
	}

	public function postResetPassword()
	{
		$credentials = Input::only(
			'email',
			'password',
			'token'
		);

		$reset = Password::reset($credentials);

		if ($reset->hasErrors())
		{
			return Redirect::back()->withErrors($auth->errors);
		}

		Notify::success('Your password was reset. Please log in.')->flash();

		return Redirect::route('login');
	}

}