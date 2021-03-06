<?php namespace Andheiberg\Auth\Facades;

use Illuminate\Support\Facades\Facade as Facade;

/**
 * @see \Andheiberg\Auth\Auth
 */
class Auth extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'auth'; }

}