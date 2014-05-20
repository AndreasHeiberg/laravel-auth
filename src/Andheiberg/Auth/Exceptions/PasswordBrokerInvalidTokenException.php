<?php namespace Andheiberg\Auth\Exceptions;

class PasswordBrokerUserNotFoundException extends AuthException {

	/**
	 * The key the error message applies to.
	 *
	 * @var string
	 */
	protected $messageKey = 'token';

	/**
	 * The error message for the exception.
	 *
	 * @var string
	 */
	protected $message = 'auth::password.token';
	
};