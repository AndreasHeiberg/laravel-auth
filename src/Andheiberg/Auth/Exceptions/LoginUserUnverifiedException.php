<?php namespace Andheiberg\Auth\Exceptions;

class LoginUserUnverifiedException extends AuthException {

	/**
	 * The key the error message applies to.
	 *
	 * @var string
	 */
	protected $messageKey = 'email';
	
	/**
	 * The error message for the exception.
	 *
	 * @var string
	 */
	protected $message = 'auth::login.unverified';
	
};