<?php namespace Andheiberg\Auth\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class SetupCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'auth:setup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Publish views, controllers and config.';

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * Create a new reminder table command instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(Filesystem $files)
	{
		parent::__construct();

		$this->files = $files;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->publishController();
		$this->publishViews();
		$this->publishConfig();
	}

	/**
	 * Publish the AuthController
	 *
	 * @return void
	 */
	public function publishController()
	{
		$destination = $this->laravel['path'].'/controllers/AuthController.php';

		if ( ! $this->files->exists($destination))
		{
			$this->files->copy(__DIR__.'/stubs/AuthController.php', $destination);

			$this->info('Auth controller created successfully!');

			$this->comment("Route: Route::controller('/', 'AuthController');");
		}
		else
		{
			$this->error('Auth controller already exists!');
		}
	}

	/**
	 * Publish the AuthController
	 *
	 * @return void
	 */
	public function publishViews()
	{
		$destination = $this->laravel['path'].'/views/auth';

		if ( ! $this->files->exists($destination))
		{
			$this->files->copyDirectory(__DIR__.'/stubs/views', $destination);

			$this->info('Auth views created successfully!');
		}
		else
		{
			$this->error('Auth views already exists!');
		}
	}

	/**
	 * Publish the AuthController
	 *
	 * @return void
	 */
	public function publishConfig()
	{
		$destination = $this->laravel['path'].'/config/auth.php';

		if ($this->confirm('This will overwrite app/config/auth.php, do you want to proceed. [yes|no]'))
		{
			$this->files->copy(__DIR__.'/stubs/config.php', $destination);

			$this->info('Auth config created successfully!');
		}
		else
		{
			$this->error('Auth config was not published!');
		}
	}

}