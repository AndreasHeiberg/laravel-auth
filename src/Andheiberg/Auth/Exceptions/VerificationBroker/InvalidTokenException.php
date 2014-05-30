<?php namespace Andheiberg\Auth\Exceptions\VerificationBroker;

use Andheiberg\Auth\Exceptions\AuthException;

class InvalidTokenException extends AuthException {

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
	protected $message = 'auth::verification.token';
	
};