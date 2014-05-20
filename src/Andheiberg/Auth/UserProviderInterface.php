<?php namespace Andheiberg\Auth;

interface UserProviderInterface {

	/**
	 * Retrieve a user by their unique identifier.
	 *
	 * @param  mixed  $identifier
	 * @return \Andheiberg\Auth\UserInterface|null
	 */
	public function retrieveById($identifier);

	/**
	 * Retrieve a user by by their unique identifier and "remember me" token.
	 *
	 * @param  mixed  $identifier
	 * @param  string  $token
	 * @return \Andheiberg\Auth\UserInterface|null
	 */
	public function retrieveByToken($identifier, $token);

	/**
	 * Update the "remember me" token for the given user in storage.
	 *
	 * @param  \Andheiberg\Auth\UserInterface  $user
	 * @param  string  $token
	 * @return void
	 */
	public function updateRememberToken(UserInterface $user, $token);

	/**
	 * Retrieve a user by the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Andheiberg\Auth\UserInterface|null
	 */
	public function retrieveByCredentials(array $credentials);

	/**
	 * Validate a user against the given credentials.
	 *
	 * @param  \Andheiberg\Auth\UserInterface  $user
	 * @param  array  $credentials
	 * @return bool
	 */
	public function validateCredentials(UserInterface $user, array $credentials);

	/**
	 * Register a new user using the given credentials.
	 *
	 * @param  array  $credentials
	 * @param  bool   $verify
	 * @param  bool   $login
	 * @param  bool   $remember
	 * @return bool
	 */
	public function register(array $credentials = array());

	/**
	 * Update the "verified" boolean for the given user in storage.
	 *
	 * @param  \Andheiberg\Auth\UserInterface  $user
	 * @param  bool   $value
	 * @return void
	 */
	public function updateAuthVerified(UserInterface $user, $value);

	/**
	 * Update the "password" for the given user in storage.
	 *
	 * @param  \Andheiberg\Auth\UserInterface  $user
	 * @param  string $value
	 * @return void
	 */
	public function updatePassword(UserInterface $user, $value);

}
