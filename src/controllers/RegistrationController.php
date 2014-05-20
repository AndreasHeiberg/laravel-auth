<?php namespace Andheiberg\Auth\Controllers;

use Auth;
use Andheiberg\Auth\AuthException;
use Controller;
use Input;
use Notify;
use Redirect;
use Validator;
use View;

class RegistrationController extends Controller {

	/**
	 * Display the registration form.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		if ( ! Auth::guest())
		{
			Notify::info('auth::register.already');

			return Redirect::route('dashboard');
		}

		return View::make('auth::register');
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * @param  string  $token
	 * @return Response
	 */
	public function postIndex($token = null)
	{
		try
		{
			$credentials = Input::except('_token');

			$validation = Validator::make($credentials, ['email' => 'required|email|unique:users,email', 'password' => 'required']);

			if ($validation->fails())
			{
				return Redirect::back()->withInput()->withErrors($validation->messages());
			}

			$auth = Auth::register($credentials);

			Notify::success('auth::register.verification', ['email' => $credentials['email']]);
		
			return Redirect::intended(route('login'));
		}
		catch (AuthException $e)
		{
			return $this->redirect->back()->withInput()->withErrors($e->messages());
		}
	}

}
