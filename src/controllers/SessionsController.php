<?php namespace Andheiberg\Auth\Controllers;

use Auth;
use Andheiberg\Auth\Exceptions\AuthException;
use Controller;
use Input;
use Notify;
use Redirect;
use Validator;
use View;
use Config;

class SessionsController extends Controller {

	/**
	 * Display the login form.
	 *
	 * @return Response
	 */
	public function create()
	{
		if ( ! Auth::guest())
		{
			Notify::info('auth.login.already');

			return Redirect::route('dashboard');
		}

		return View::make('auth::login');
	}

	/**
	 * Log the user in.
	 *
	 * @return Response
	 */
	public function store()
	{
		try
		{
			$credentials = Input::only(['email', 'password']);

			$validation = Validator::make($credentials, ['email' => 'required|email', 'password' => 'required']);

			if ($validation->fails())
			{
				return Redirect::back()->withInput()->withErrors($validation->messages());
			}

			Auth::attempt($credentials, true);

			Notify::info('auth::login.success', ['name' => Auth::user()->first_name]);

			return Redirect::intended(route(Config::get('auth::successful-login-route')));
		}
		catch (AuthException $e)
		{
			return Redirect::back()->withInput()->withErrors($e->messages());
		}
	}

	/**
	 * Log the user out.
	 *
	 * @return Response
	 */
	public function destroy()
	{
		Auth::logout();

		Notify::success('auth::logout.goodby');
		
		return Redirect::route('login');
	}

}
