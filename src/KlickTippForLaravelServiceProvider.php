<?php
/*
 * Copyright (c) - WDigital - 2022. 
 * @link https://wdigital.ch
 * @developer Florian Würtenberger <florian@wdigital.ch>
 */

namespace WDigital\KlickTippForLaravel;

use Illuminate\Support\ServiceProvider;

/**
 * Class KlickTippForLaravelServiceProvider
 *
 * @package WDigital\KlickTippForLaravel
 */
class KlickTippForLaravelServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 */
	public function boot()
	{
		/*
		 * Optional methods to load your package assets
		 */
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__.'/../config/config.php' => config_path('klicktipp.php'),
			], 'config');
		}
	}

	/**
	 * Register the application services.
	 */
	public function register()
	{
		// Automatically apply the package configuration
		$this->mergeConfigFrom(__DIR__.'/../config/config.php', 'klicktipp.php');
	}
}
