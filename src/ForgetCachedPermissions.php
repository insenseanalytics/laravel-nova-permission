<?php

namespace Insenseanalytics\LaravelNovaPermission;

use Laravel\Nova\Nova;
use Spatie\Permission\PermissionRegistrar;

class ForgetCachedPermissions
{
	/**
	 * Handle the incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure                 $next
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function handle($request, $next)
	{
		$response = $next($request);

		if ($request->is('nova-api/*/detach') ||
			$request->is('nova-api/*/*/attach*/*')) {
			$permissionKey = (Nova::resourceForModel(app(PermissionRegistrar::class)->getPermissionClass()))::uriKey();

			if ($request->viaRelationship === $permissionKey) {
				app(PermissionRegistrar::class)->forgetCachedPermissions();
			}
		}

		return $response;
	}
}
