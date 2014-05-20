<?php namespace Andheiberg\Auth;

use Illuminate\Support\Manager;

class UserProviderManager extends Manager {

	/**
	 * Create an instance of the database user provider.
	 *
	 * @return \Andheiberg\Auth\DatabaseUserProvider
	 */
	public function createDatabaseDriver()
	{
		$connection = $this->app['db']->connection();

		// When using the basic database user provider, we need to inject the table we
		// want to use, since this is not an Eloquent model we will have no way to
		// know without telling the provider, so we'll inject the config value.
		$table = $this->app['config']['auth::table'];

		return new DatabaseUserProvider($connection, $this->app['hash'], $table);
	}

	/**
	 * Create an instance of the Eloquent user provider.
	 *
	 * @return \Andheiberg\Auth\EloquentUserProvider
	 */
	public function createEloquentDriver()
	{
		$model = $this->app['config']['auth::model'];

		return new EloquentUserProvider($this->app['hash'], $model);
	}

	/**
	 * Get the default authentication driver name.
	 *
	 * @return string
	 */
	public function getDefaultDriver()
	{
		return $this->app['config']['auth::driver'];
	}

	/**
	 * Set the default authentication driver name.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function setDefaultDriver($name)
	{
		$this->app['config']['auth::driver'] = $name;
	}

}
