<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 2018/12/14
 * Time: 16:03
 */

namespace Insenseanalytics\LaravelNovaPermission;


use Laravel\Nova\Http\Requests\NovaRequest;

trait TranslationHandelTrait {

	/**
	 * Override the applyFilters method to add the guard_name condition when filtering
	 *
	 * @param  \Laravel\Nova\Http\Requests\NovaRequest $request
	 * @param  \Illuminate\Database\Eloquent\Builder $query
	 * @param  array $filters
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	protected static function applyFilters(NovaRequest $request, $query, array $filters) {
		$query = parent::applyFilters($request, $query, $filters);
		if ($model = head($request->__memoized)) {
			$guard_name = $model->guard_name ?? self::getGuardForModel(get_class($model));
			$query->where('guard_name', $guard_name);
		}

		return $query;
	}

	/**
	 * @param string model
	 *
	 * @return string|null
	 */
	public static function getGuardForModel(string $model) {

		return collect(config('auth.guards'))
			->map(function ($guard) {
				return config("auth.providers.{$guard['provider']}.model");
			})->search($model);
	}
}