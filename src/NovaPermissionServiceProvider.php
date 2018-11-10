<?php

namespace Insenseanalytics\LaravelNovaPermission;

use Illuminate\Support\ServiceProvider;

class NovaPermissionServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 */
	public function boot()
	{
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-nova-permission');
	}

	/**
	 * Register any application services.
	 */
	public function register()
	{
	}
}
