<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;

class DeployNotification extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'deploy:notification {env}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send an email after deploy';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$env = $this->argument('env');

		Mail::send('core.emails.notification.deploy', compact('env'), function ($m) use ($env) {
			$m->from('alert@mbiance.com', 'Alerte deployment');
			$m->to('dev@mbiance.com')->subject('Deployment');
		});
	}
}
