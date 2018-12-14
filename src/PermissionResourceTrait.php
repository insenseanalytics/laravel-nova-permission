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


    /**
     * Override the applyFilters method,title field translation
     */
    public function title() {

        return __('laravel-nova-permission::permissions.display_names.'.$this->name);
    }

    /**
     * Override the applyFilters method to add the guard_name condition when filtering
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyFilters(NovaRequest $request, $query, array $filters)
    {
        $query = parent::applyFilters($request, $query, $filters);
        if($model = head($request->__memoized)){
            $guard_name = $model->guard_name ?? getGuardForModel(get_class($model));
            $query->where('guard_name', $guard_name);
        }

        return $query;
    }

    /**
     * Rewrite the applySearch method to apply translation field search
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applySearch($query, $search)
    {
        $trans_search = array_keys(preg_grep("/$search/",array_dot(__('laravel-nova-permission::permissions.display_names'))));

        if (is_numeric($search) && in_array($query->getModel()->getKeyType(), ['int', 'integer'])) {
            $query->whereKey($search);
        }

        return $query->where(function ($query) use ($trans_search) {
            $model = $query->getModel();

            foreach (static::searchableColumns() as $column) {
                foreach ($trans_search as $search){
                    $query->orWhere($model->qualifyColumn($column), 'like', '%'.$search.'%');
                }
            }
        });
    }
}
