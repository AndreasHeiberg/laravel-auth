<?php namespace Andheiberg\Auth\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class LangCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'auth:lang';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Publish language files to app/lang/packages';

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
		$destination = $this->laravel['path'].'/lang/packages/en/auth';

		if ( ! $this->files->exists($destination))
		{
			$this->files->copyDirectory(__DIR__.'/../../../lang/en', $destination);

			$this->info('Auth language files created successfully!');
		}
		else
		{
			$this->error('Auth language files already exists!');
		}
	}

}
