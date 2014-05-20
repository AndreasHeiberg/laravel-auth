<?php namespace Andheiberg\Auth;

trait UserTrait {

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Set the password for the user.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function setAuthPassword($value)
	{
		$this->password = $value;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
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
		return $this->auth_verified;
	}

	/**
	 * Set value of auth verified.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function setAuthVerified($value)
	{
		return $this->auth_verified = $value;
	}

	/**
	 * Check if a user has been deactivated.
	 *
	 * @return string
	 */
	public function getAuthDeactivated()
	{
		return $this->auth_deactivated;
	}

	/**
	 * Set value of auth deactived.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function setAuthDeactivated($value)
	{
		return $this->auth_deactivated = $value;
	}

}
