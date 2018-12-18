<?php

namespace Insenseanalytics\LaravelNovaPermission;

use Laravel\Nova\Http\Requests\NovaRequest;
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

trait PermissionResourceTrait
{

	/**
	 * Override the applyFilters method,title field translation
	 */
	public function title() {

		return array_has(__('laravel-nova-permission::permissions.display_names'),$this->name)
			? __("laravel-nova-permission::permissions.display_names.{$this->name}")
			: $this->{static::$title};
	}

	public static function getModel()
	{
		return app(PermissionRegistrar::class)->getPermissionClass();
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

		$roleResource = Nova::resourceForModel(app(PermissionRegistrar::class)->getRoleClass());

		return [
			ID::make()->sortable(),

			Text::make(__('laravel-nova-permission::permissions.name'), 'name')
				->rules(['required', 'string', 'max:255'])
				->creationRules('unique:' . config('permission.table_names.permissions'))
				->updateRules('unique:' . config('permission.table_names.permissions') . ',name,{{resourceId}}'),

			Text::make(__('laravel-nova-permission::permissions.display_name'),function (){
				return __('laravel-nova-permission::permissions.display_names.'.$this->name);
			})->canSee(function (){
				return is_array(__('laravel-nova-permission::permissions.display_names'));
			}),

			Select::make(__('laravel-nova-permission::permissions.guard_name'), 'guard_name')
				->options($guardOptions->toArray())
				->rules(['required', Rule::in($guardOptions)]),

			DateTime::make(__('laravel-nova-permission::permissions.created_at'), 'created_at')->exceptOnForms(),

			DateTime::make(__('laravel-nova-permission::permissions.updated_at'), 'updated_at')->exceptOnForms(),

			BelongsToMany::make($roleResource::label(), 'roles', $roleResource)->searchable(),

			MorphToMany::make($userResource::label(), 'users', $userResource)->searchable(),
		];
	}
}
