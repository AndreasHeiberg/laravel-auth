<?php namespace Controllers;

use Models\User;
use \App;

class AuthController extends BaseController {

	/**
	 * The password instance.
	 *
	 * @var Models\User
	 */
	protected $password;

	/**
	 * Create a BaseController instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->password = App::make('auth.password-reminder');
	}

	public function getLogin()
	{
		if ( ! $this->auth->guest())
		{
			$this->notify->info("You are already logged in.")->flash();

			return $this->redirect->route('dashboard');
		}

		return $this->view->make('auth.login');
	}

	public function postLogin()
	{
		$email = $this->input->get('email');
		$password = $this->input->get('password');

		$auth = $this->auth->attempt(compact('email', 'password'), true);

		if ($auth->hasErrors())
		{
			return $this->redirect->back()->withInput()->withErrors( $auth->errors );
		}

		$user = $this->auth->user();

		$this->notify->info("Welcome, {$user->first_name}!")->flash();

		return $this->redirect->intended(route('dashboard'));
	}

	public function getLogout()
	{
		$this->auth->logout();

		$this->notify->success('Thanks for using [product] it means a lot to us. See you next time.')->flash();
		
		return $this->redirect->route('login');
	}

	/**
	 * Show the form for creating a new user.
	 *
	 * @return Response
	 */
	public function getRegister()
	{
		if ( ! $this->auth->guest())
		{
			$this->notify->info("You can't register as you already have an account.")->flash();

			return $this->redirect->route('dashboard');
		}

		return $this->view->make('auth.register');
	}

	/**
	 * Store a newly created user in storage.
	 *
	 * @return Response
	 */
	public function postRegister()
	{
		$credentials = $this->input->except('_token');

		$auth = $this->auth->register($credentials);

		if ($auth->hasErrors())
		{
			return $this->redirect->back()->withInput()->withErrors($auth->errors);
		}

		$this->notify->success("A verification email has been sent to {$credentials['email']}. <b>Please check your inbox and your spam filter.</b>")->flash();
		
		return $this->redirect->intended(route('login'));
	}

	/**
	 * Show the form for creating a new user.
	 *
	 * @return Response
	 */
	public function getVerifyEmail()
	{
		$credentials = $this->input->only('email', 'token');

		$auth = $this->auth->verifyEmail($credentials);

		if ($auth->hasErrors())
		{
			dd($auth->errors);
			return $this->redirect->route('forgot-password')->hasErrors($auth->errors);
		}

		$this->notify->success('Your email has been verified.')->flash();

		$this->auth->loginUsingEmail($credentials['email']);

		return $this->redirect->route('dashboard');
	}

	public function getForgotPassword()
	{
		return $this->view->make('auth.forgot-password');
	}

	public function postForgotPassword()
	{
		$reminder = $this->password->remind(['email' => $this->input->get('email')]);
		
		if ($reminder->hasErrors())
		{
			return $this->redirect->back()->withErrors($reminder->errors);
		}

		$this->notify->success("We have sent you an email with further information on how to reset your password.")->flash();

		return $this->redirect->route('login');
	}

	public function getResetPassword()
	{
		$email = $this->input->get('email');
		$token = $this->input->get('token');

		return $this->view->make('auth.reset-password', compact('email', 'token'));
	}

	public function postResetPassword()
	{
		$credentials = $this->input->only(
			'email',
			'password',
			'token'
		);

		$reset = $this->password->reset($credentials);

		if ($reset->hasErrors())
		{
			return $this->redirect->back()->withErrors($auth->errors);
		}

		$this->notify->success('Your password was reset. Please log in.')->flash();

		return $this->redirect->route('login');
	}

}