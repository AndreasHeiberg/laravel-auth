<?php namespace Andheiberg\Auth\Exceptions;

use Exception;
use Lang;

class AuthException extends Exception {

	/**
	 * The key the error message applies to.
	 *
	 * @var string
	 */
	protected $messageKey;

	/**
	 * The error message for the exception.
	 *
	 * @var string
	 */
	protected $message;

	public function messages()
	{
		$message = $this->message;

		if (Lang::has($message))
		{
			$message = Lang::get($message);
		}

		return [$this->messageKey => $message];
	}
};