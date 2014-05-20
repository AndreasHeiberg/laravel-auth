<?php namespace Andheiberg\Auth\Controllers;

use App;
use AuthVerification;
use Andheiberg\Auth\Exceptions\AuthException;
use Controller;
use Input;
use Notify;
use Redirect;
use View;

class VerificationController extends Controller {

	/**
	 * Display the verification reminder view.
	 *
	 * @return Response
	 */
	public function getSendVerificationEmail()
	{
		return View::make('auth::verification.send');
	}

	/**
	 * Send the verification reminder.
	 *
	 * @return Response
	 */
	public function postSendVerificationEmail()
	{
		try
		{
			AuthVerification::remind(Input::only('email'));
			
			Notify::success('auth::verification.sent');

			return Redirect::back();
		}
		catch (AuthException $e)
		{
			return Redirect::back()->withErrors($e->messages());
		}
	}

	/**
	 * Verify an email.
	 *
	 * @return Response
	 */
	public function getVerify()
	{
		if ( ! Input::has('token'))
		{
			App::abort(404);
		}

		try
		{
			AuthVerification::verify(Input::all());

			Notify::success('auth::verification.verified');

			return Redirect::route('login');
		}
		catch (AuthException $e)
		{
			return Redirect::back()->withErrors($e->messages());	
		}
	}

}
