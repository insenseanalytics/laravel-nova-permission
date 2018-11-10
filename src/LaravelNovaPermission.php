<?php

namespace Insenseanalytics\LaravelNovaPermission;

use Laravel\Nova\Tool;
use Laravel\Nova\Nova;

class LaravelNovaPermission extends Tool
{
	public $roleResource = Role::class;
	public $permissionResource = Permission::class;

	public $registerCustomResources = false;

	/**
	 * Perform any tasks that need to happen when the tool is booted.
	 */
	public function boot()
	{
		if ((Role::class === $this->roleResource && Permission::class === $this->permissionResource)
			|| $this->registerCustomResources) {
			Nova::resources([
				$this->roleResource,
				$this->permissionResource,
			]);
		}
	}

	/**
	 * Set a custom Role resource class.
	 *
	 * @param Role resource class
	 *
	 * @return $this
	 */
	public function roleResource(string $roleResource)
	{
		$this->roleResource = $roleResource;

		return $this;
	}

	/**
	 * Set a custom Permission resource class.
	 *
	 * @param Permission resource class
	 *
	 * @return $this
	 */
	public function permissionResource(string $permissionResource)
	{
		$this->permissionResource = $permissionResource;

		return $this;
	}

	/**
	 * Register the custom resource classes.
	 *
	 * @param bool
	 *
	 * @return $this
	 */
	public function withRegistration()
	{
		$this->registerCustomResources = true;

		return $this;
	}

	/**
	 * Build the view that renders the navigation links for the tool.
	 *
	 * @return \Illuminate\View\View
	 */
	public function renderNavigation()
	{
		return view('laravel-nova-permission::navigation');
	}
}
