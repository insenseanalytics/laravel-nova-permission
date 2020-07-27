<?php

namespace Mukulsmu\LaravelNovaPermission;

use Laravel\Nova\Nova;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\BelongsToMany;
use Spatie\Permission\PermissionRegistrar;

trait RoleResourceTrait
{
	public static function getModel()
	{
		return app(PermissionRegistrar::class)->getRoleClass();
	}

	/**
	 * Get the fields displayed by the resource.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return array
	 */
	public function fields(Request $request)
	{
		$guardOptions = collect(config('auth.guards'))->mapWithKeys(function ($value, $key) {
			return [$key => $key];
		});

		$userResource = Nova::resourceForModel(getModelForGuard($this->guard_name));

		$permissionResource = Nova::resourceForModel(app(PermissionRegistrar::class)->getPermissionClass());

		return [
			ID::make()->sortable(),

			Text::make(__('laravel-nova-permission::roles.name'), 'name')
				->rules(['required', 'string', 'max:255'])
				->creationRules('unique:' . config('permission.table_names.roles'))
				->updateRules('unique:' . config('permission.table_names.roles') . ',name,{{resourceId}}'),

			Select::make(__('laravel-nova-permission::roles.guard_name'), 'guard_name')
				->options($guardOptions->toArray())
				->rules(['required', Rule::in($guardOptions)]),

			DateTime::make(__('laravel-nova-permission::roles.created_at'), 'created_at')->exceptOnForms(),

			DateTime::make(__('laravel-nova-permission::roles.updated_at'), 'updated_at')->exceptOnForms(),

			BelongsToMany::make($permissionResource::label(), 'permissions', $permissionResource)->searchable(),

			MorphToMany::make($userResource::label(), 'users', $userResource)->searchable(),
		];
	}
}
