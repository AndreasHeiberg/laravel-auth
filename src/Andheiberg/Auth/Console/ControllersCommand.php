<?php namespace Andheiberg\Auth\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ControllersCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'auth:controllers';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create example auth controllers';

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
		$destination = $this->laravel['path'].'/controllers/Auth';

		if ( ! $this->files->exists($destination))
		{
			$this->files->copyDirectory(__DIR__.'/../../../controllers', $destination);

			$this->info('Auth controllers created successfully!');

			$this->comment("Route: Route::controller('auth', 'Controllers\Auth\AuthController');");
			$this->comment("Route: Route::controller('password', 'Controllers\Auth\PasswordController');");
			$this->comment("Route: Route::controller('verification', 'Controllers\Auth\VerificationController');");
		}
		else
		{
			$this->error('Auth controllers already exists!');
		}
	}

}
