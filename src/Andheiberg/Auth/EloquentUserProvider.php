<?php namespace Andheiberg\Auth;

use Illuminate\Hashing\HasherInterface;

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
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public function retrieveById($identifier)
	{
		return $this->createModel()->newQuery()->find($identifier);
	}

	/**
	 * Retrieve a user by their email.
	 *
	 * @param  mixed  $identifier
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public function retrieveByEmail($email)
	{
		return $this->createModel()->newQuery()->where('email', $email)->first();
	}

	/**
	 * Retrieve a user by the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Illuminate\Auth\UserInterface|null
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
	 * @param  \Illuminate\Auth\UserInterface  $user
	 * @param  array  $credentials
	 * @return bool
	 */
	public function validateCredentials(UserInterface $user, array $credentials)
	{
		$plain = $credentials['password'];

		// Is user password is valid?
		if( ! $this->hasher->check($plain, $user->getAuthPassword()))
		{
			throw new UserPasswordIncorrectException('User password is incorrect');
		}

		// Valid user, but are they verified?
		if (isset($user->auth_email_verified) and ! $user->auth_email_verified)
		{
			throw new UserUnverifiedException('User is unverified');
		}

		// Is the user deleted?
		if (isset($user->deleted_at) and $user->deleted_at !== NULL)
		{
			throw new UserDeletedException('User is deleted');
		}

		return true;
	}

	/**
	 * Register a user with the given credentials
	 *
	 * @param  array  $credentials
	 * @param  bool   $login
	 * @return bool
	 */
	public function register(array $credentials, $login = false)
	{
		$user = $this->createModel()->create($credentials);
		
		if ($login)
		{
			$user->auth_email_verified = true;
			$user->save();
		}

		return $user;
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
