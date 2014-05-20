<?php namespace Andheiberg\Auth;

use Illuminate\Support\ServiceProvider;
use Andheiberg\Auth\Console\UsersTableCommand;
use Andheiberg\Auth\Console\RemindersTableCommand;
use Andheiberg\Auth\Console\ClearRemindersCommand;
use Andheiberg\Auth\Console\ControllersCommand;
use Andheiberg\Auth\Console\LangCommand;
use Andheiberg\Auth\Reminders\DatabaseReminderRepository as DbRepository;
use Andheiberg\Auth\Reminders\VerificationBroker;
use Andheiberg\Auth\Reminders\PasswordBroker;

class AuthServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('andheiberg/auth');

		if ($this->app['config']->get('auth::routes'))
		{
			require __DIR__.'/../../routes.php';
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerAuth();

		$this->registerVerificationBroker();

		$this->registerPasswordBroker();

		$this->registerReminderRepository();

		$this->registerCommands();
	}

	/**
	 * Register the auth instance.
	 *
	 * @return void
	 */
	protected function registerAuth()
	{
		$this->app->bindShared('auth', function($app)
		{
			// Once the authentication service has actually been requested by the developer
			// we will set a variable in the application indicating such. This helps us
			// know that we need to set any queued cookies in the after event later.
			$app['auth.loaded'] = true;

			$provider = new UserProviderManager($app);

			return new AuthManager($app, $provider);
		});
	}

	/**
	 * Register the verification broker instance.
	 *
	 * @return void
	 */
	protected function registerVerificationBroker()
	{
		$this->app->bindShared('auth.reminder.verification', function($app)
		{
			// The reminder repository is responsible for storing the user e-mail addresses
			// and verification tokens. It will be used to verify the tokens are valid
			// for the given e-mail addresses. We will resolve an implementation here.
			$reminders = $app['auth.reminder.repository'];

			$users = (new UserProviderManager($app))->driver();

			$view = 'auth::emails.verification';

			// The verification broker uses the reminder repository to validate tokens and send
			// reminder e-mails, as well as validating the verification process as an
			// aggregate service of sorts providing a convenient interface for verification.
			return new VerificationBroker(

				$reminders, $users, $app['mailer'], $view

			);
		});
	}

	/**
	 * Register the password broker instance.
	 *
	 * @return void
	 */
	protected function registerPasswordBroker()
	{
		$this->app->bindShared('auth.reminder.password', function($app)
		{
			// The reminder repository is responsible for storing the user e-mail addresses
			// and password reset tokens. It will be used to verify the tokens are valid
			// for the given e-mail addresses. We will resolve an implementation here.
			$reminders = $app['auth.reminder.repository'];

			$users = (new UserProviderManager($app))->driver();

			$view = 'auth::emails.password';

			// The password broker uses the reminder repository to validate tokens and send
			// reminder e-mails, as well as validating the password reset process as an
			// aggregate service of sorts providing a convenient interface for resets.
			return new PasswordBroker(

				$reminders, $users, $app['mailer'], $view

			);
		});
	}

	/**
	 * Register the reminder repository implementation.
	 *
	 * @return void
	 */
	protected function registerReminderRepository()
	{
		$this->app->bindShared('auth.reminder.repository', function($app)
		{
			$connection = $app['db']->connection();

			// The database reminder repository is an implementation of the reminder repo
			// interface, and is responsible for the actual storing of auth tokens and
			// their e-mail addresses. We will inject this table and hash key to it.
			$table = $app['config']['auth.reminder.table'];

			$key = $app['config']['app.key'];

			$expire = $app['config']->get('auth.reminder.expire', 60);

			return new DbRepository($connection, $table, $key, $expire);
		});
	}

	/**
	 * Register the auth related console commands.
	 *
	 * @return void
	 */
	protected function registerCommands()
	{
		$this->app->bindShared('command.auth.users', function($app)
		{
			return new UsersTableCommand($app['files']);
		});

		$this->app->bindShared('command.auth.reminders', function($app)
		{
			return new RemindersTableCommand($app['files']);
		});

		$this->app->bindShared('command.auth.reminders.clear', function($app)
		{
			return new ClearRemindersCommand;
		});

		$this->app->bindShared('command.auth.controller', function($app)
		{
			return new ControllersCommand($app['files']);
		});

		$this->app->bindShared('command.auth.lang', function($app)
		{
			return new LangCommand($app['files']);
		});

		$this->commands(
			'command.auth.users',
			'command.auth.reminders',
			'command.auth.reminders.clear',
			'command.auth.controller',
			'command.auth.lang'
		);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'auth',
			'auth.reminder.verification',
			'auth.reminder.password',
			'auth.reminder.repository',
			'command.auth.reminders'
		);
	}

}
