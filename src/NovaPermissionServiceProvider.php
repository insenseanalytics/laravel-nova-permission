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
		$this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-nova-permission');

		$this->publishes([
			__DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-nova-permission'),
		], 'laravel-nova-permission-lang');
	}

	/**
	 * Register any application services.
	 */
	public function register()
	{
	}
}
