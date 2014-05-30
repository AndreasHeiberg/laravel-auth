<?php namespace Andheiberg\Auth\Exceptions\Login;

use Andheiberg\Auth\Exceptions\AuthException;

class UserNotFoundException extends AuthException {

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
	protected $message = 'auth::login.user';
	
};