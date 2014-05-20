<?php namespace Andheiberg\Auth\Controllers;

use App;
use Auth;
use Andheiberg\Auth\Exceptions\AuthException;
use Controller;
use Input;
use Notify;
use Password;
use Redirect;
use View;
use Hash;

class PasswordController extends Controller {

	/**
	 * Display the password reminder view.
	 *
	 * @return Response
	 */
	public function getForgot()
	{
		return View::make('auth::password.remind');
	}

	/**
	 * Handle a POST request to remind a user of their password.
	 *
	 * @return Response
	 */
	public function postForgot()
	{
		try
		{
			Password::remind(Input::only('email'));
			
			Notify::success('auth::password.sent');

			return Redirect::back();
		}
		catch (AuthException $e)
		{
			return Redirect::back()->withErrors($e->messages());
		}
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * @return Response
	 */
	public function getReset()
	{
		if ( ! Input::has('token') or ! Input::has('email'))
		{
			App::abort(404);
		}

		return View::make('auth::password.reset', Input::only(['token', 'email']));
	}

	/**
	 * Handle a POST request to reset a user's password.
	 *
	 * @return Response
	 */
	public function postReset()
	{
		try
		{
			$credentials = Input::only(
				'email',
				'password',
				'password_confirmation',
				'token'
			);

			Password::reset($credentials);

			Notify::success('auth::password.reset');

			return Redirect::to('login');
		}
		catch (AuthException $e)
		{
			return Redirect::back()->withInput()->withErrors($e->messages());
		}
	}

}
