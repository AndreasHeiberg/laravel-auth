<?php namespace Andheiberg\Auth;

use Illuminate\Hashing\HasherInterface;
use Andheiberg\Auth\Exceptions\Login\IncorrectPasswordException;
use Andheiberg\Auth\Exceptions\Login\UserDeactivatedException;
use Andheiberg\Auth\Exceptions\Login\UserNotFoundException;
use Andheiberg\Auth\Exceptions\Login\UserUnverifiedException;

class EloquentUserProvider implements UserProviderInterface {

	/**
	 * The hasher implementation.
	 *
	 * @var \Illuminate\Hashing\HasherInterface
	 */
	protected $hasher;

	/**
	 * The Eloquent user model.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * Create a new database user provider.
	 *
	 * @param  \Illuminate\Hashing\HasherInterface  $hasher
	 * @param  string  $model
	 * @return void
	 */
	public function __construct(HasherInterface $hasher, $model)
	{
		$this->model = $model;
		$this->hasher = $hasher;
	}

	/**
	 * Retrieve a user by their unique identifier.
	 *
	 * @param  mixed  $identifier
	 * @return \Andheiberg\Auth\UserInterface|null
	 */
	public function retrieveById($identifier)
	{
		return $this->createModel()->newQuery()->find($identifier);
	}

	/**
	 * Retrieve a user by by their unique identifier and "remember me" token.
	 *
	 * @param  mixed  $identifier
	 * @param  string  $token
	 * @return \Andheiberg\Auth\UserInterface|null
	 */
	public function retrieveByToken($identifier, $token)
	{
		$model = $this->createModel();

		return $model->newQuery()
                        ->where($model->getKeyName(), $identifier)
                        ->where($model->getRememberTokenName(), $token)
                        ->first();
	}

	/**
	 * Update the "remember me" token for the given user in storage.
	 *
	 * @param  \Andheiberg\Auth\UserInterface  $user
	 * @param  string  $token
	 * @return void
	 */
	public function updateRememberToken(UserInterface $user, $token)
	{
		$user->setAttribute($user->getRememberTokenName(), $token);

		$user->save();
	}

	/**
	 * Retrieve a user by the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Andheiberg\Auth\UserInterface|null
	 */
	public function retrieveByCredentials(array $credentials)
	{
		// First we will add each credential element to the query as a where clause.
		// Then we can execute the query and, if we found a user, return it in a
		// Eloquent User "model" that will be utilized by the Guard instances.
		$query = $this->createModel()->newQuery();

		foreach ($credentials as $key => $value)
		{
			if ( ! str_contains($key, 'password')) $query->where($key, $value);
		}

		return $query->first();
	}

	/**
	 * Validate a user against the given credentials.
	 *
	 * @param  \Andheiberg\Auth\UserInterface  $user
	 * @param  array  $credentials
	 * @return bool
	 */
	public function validateCredentials(UserInterface $user, array $credentials)
	{
		$plain = $credentials['password'];

		if ( ! $this->hasher->check($plain, $user->getAuthPassword()))
		{
			throw new IncorrectPasswordException;
		}

		if ( ! $user->getAuthVerified())
		{
			throw new UserUnverifiedException;
		}

		if ($user->getAuthDeactivated())
		{
			throw new UserDeactivatedException;
		}

		return true;
	}

	/**
	 * Register a new user using the given credentials.
	 *
	 * @param  array  $credentials
	 * @param  bool   $verify
	 * @return bool
	 */
	public function register(array $credentials = array(), $verify = true)
	{
		$credentials['password'] = $this->hasher->make($credentials['password']);
		
		$user = $this->createModel()->create($credentials);

		if ( ! $verify)
		{
			$this->updateAuthVerified($user, true);
		}

		return $user;
	}

	/**
	 * Update the "verified" boolean for the given user in storage.
	 *
	 * @param  \Andheiberg\Auth\UserInterface  $user
	 * @param  bool   $value
	 * @return void
	 */
	public function updateAuthVerified(UserInterface $user, $value)
	{
		$user->setAuthVerified($value);

		$user->save();
	}

	/**
	 * Update the "password" for the given user in storage.
	 *
	 * @param  \Andheiberg\Auth\UserInterface  $user
	 * @param  string $value
	 * @return void
	 */
	public function updatePassword(UserInterface $user, $value)
	{
		$hash = $this->hasher->make($value);

		$user->setAuthPassword($hash);

		$user->save();
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

}
