<?php namespace Andheiberg\Auth;

class GenericUser implements UserInterface {

	/**
	 * All of the user's attributes.
	 *
	 * @var array
	 */
	protected $attributes;

	/**
	 * Create a new generic User object.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	public function __construct(array $attributes)
	{
		$this->attributes = $attributes;
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->attributes['id'];
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->attributes['password'];
	}

	/**
	 * Set the password for the user.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function setAuthPassword($value)
	{
		$this->attributes['password'] = $value;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->attributes['remember_token'];
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->attributes['remember_token'] = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'remember_token';
	}

	/**
	 * Check if a user is verified.
	 *
	 * @return string
	 */
	public function getAuthVerified()
	{
		return $this->attributes['auth_verified'];
	}

	/**
	 * Set value of auth verified.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function setAuthVerified($value)
	{
		return $this->attributes['auth_verified'] = $value;
	}

	/**
	 * Check if a user has been deactivated.
	 *
	 * @return string
	 */
	public function getAuthDeactivated()
	{
		return $this->attributes['auth_deactivated'];
	}

	/**
	 * Set value of auth deactived.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function setAuthDeactivated($value)
	{
		return $this->attributes['auth_deactivated'] = $value;
	}

	/**
	 * Dynamically access the user's attributes.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->attributes[$key];
	}

	/**
	 * Dynamically set an attribute on the user.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->attributes[$key] = $value;
	}

	/**
	 * Dynamically check if a value is set on the user.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function __isset($key)
	{
		return isset($this->attributes[$key]);
	}

	/**
	 * Dynamically unset a value on the user.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function __unset($key)
	{
		unset($this->attributes[$key]);
	}

}
