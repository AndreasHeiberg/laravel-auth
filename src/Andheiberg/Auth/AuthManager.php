<?php namespace Andheiberg\Auth;

use Illuminate\Support\Manager;

class AuthManager extends Manager {

	/**
	 * The ProviderManager instance.
	 *
	 * @var \Andheiberg\Auth\ProviderManager
	 */
	protected $providers;

	/**
	 * Create a new manager instance.
	 *
	 * @param  \Illuminate\Foundation\Application   $app
	 * @param  \Andheiberg\Auth\UserProviderManager $providers
	 * @return void
	 */
	public function __construct($app, UserProviderManager $providers)
	{
		parent::__construct($app);

		$this->providers = $providers;
	}

	/**
	 * Create a new driver instance.
	 *
	 * @param  string  $driver
	 * @return mixed
	 */
	protected function createDriver($driver)
	{
		$guard = parent::createDriver($driver);

		// When using the remember me functionality of the authentication services we
		// will need to be set the encryption instance of the guard, which allows
		// secure, encrypted cookie values to get generated for those cookies.
		$guard->setCookieJar($this->app['cookie']);

		$guard->setDispatcher($this->app['events']);

		return $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));
	}

	/**
	 * Call a custom driver creator.
	 *
	 * @param  string  $driver
	 * @return mixed
	 */
	protected function callCustomCreator($driver)
	{
		$custom = parent::callCustomCreator($driver);

		if ($custom instanceof Guard) return $custom;

		return new Guard($custom, $this->app['auth.reminder.verification'], $this->app['session.store']);
	}

	/**
	 * Create an instance of the database driver.
	 *
	 * @return \Andheiberg\Auth\Guard
	 */
	public function createDatabaseDriver()
	{
		$provider = $this->providers->createDatabaseDriver();

		return new Guard($provider, $this->app['auth.reminder.verification'], $this->app['session.store']);
	}

	/**
	 * Create an instance of the Eloquent driver.
	 *
	 * @return \Andheiberg\Auth\Guard
	 */
	public function createEloquentDriver()
	{
		$provider = $this->providers->createEloquentDriver();

		return new Guard($provider, $this->app['auth.reminder.verification'], $this->app['session.store']);
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
