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
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//Publishes config
		$this->publishConfig();
	}

	/**
	 * Load and publishes the configuration file.
	 */
	public function publishConfig()
	{
		$this->publishes([
			__DIR__ . '/../config/klicktipp-for-laravel.php' => config_path('klicktipp-for-laravel.php'),
		], 'klicktipp-config');
	}

}
