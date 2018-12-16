<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 2018/12/15
 * Time: 10:26
 */

namespace Insenseanalytics\LaravelNovaPermission;


trait PermissionSearchTranslationTrait {

	use TranslationHandelTrait;

	/**
	 * Override the applyFilters method,title field translation
	 */
	public function title() {

		return __('laravel-nova-permission::permissions.display_names.'.$this->name);
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
		return $query->where(function ($query) use ($search) {
			if (is_numeric($search) && in_array($query->getModel()->getKeyType(), ['int', 'integer'])) {
				$query->orWhere($query->getModel()->getQualifiedKeyName(), $search);
			}

			$model = $query->getModel();

			$connectionType = $query->getModel()->getConnection()->getDriverName();

			$likeOperator = $connectionType == 'pgsql' ? 'ilike' : 'like';

			$trans_search = array_keys(preg_grep("/$search/",array_dot(__('laravel-nova-permission::permissions.display_names'))));

			foreach (static::searchableColumns() as $column) {
				$qualify_column = $model->qualifyColumn($column);
				foreach ($trans_search as $t_search){
					$query->orWhere($qualify_column, $likeOperator, '%'.$t_search.'%');
				}
				$query->orWhere($qualify_column, $likeOperator, '%'.$search.'%');
			}
		});
	}
}