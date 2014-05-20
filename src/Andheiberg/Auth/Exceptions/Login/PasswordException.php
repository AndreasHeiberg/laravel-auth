<?php namespace Andheiberg\Auth\Exceptions\Login;

class IncorrectPasswordException extends AuthException {

	/**
	 * The key the error message applies to.
	 *
	 * @var string
	 */
	protected $messageKey = 'password';

	/**
	 * The error message for the exception.
	 *
	 * @var string
	 */
	protected $message = 'auth::login.password';
	
};