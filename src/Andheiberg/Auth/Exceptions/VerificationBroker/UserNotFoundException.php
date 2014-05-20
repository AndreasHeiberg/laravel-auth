<?php namespace Andheiberg\Auth\Exceptions\VerificationBroker;

class UserNotFoundException extends AuthException {

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
	protected $message = 'auth::verification.user';
	
};