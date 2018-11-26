<?php

namespace Stylemix\Base\Eloquent;

use Carbon\Carbon;

trait DateFixes
{

	/**
	 * Create a new model instance that is existing.
	 *
	 * @param  array  $attributes
	 * @param  string|null  $connection
	 * @return static
	 */
	public function newFromBuilder($attributes = [], $connection = null)
	{
		$attributes = (array) $attributes;

		$dates = collect($this->getCasts())
			->intersect(['date', 'datetime'])
			->keys()
			->merge($this->getDates())
			->all();

		foreach ($dates as $key) {
			if (! isset($attributes[$key])) {
				continue;
			}

			$attributes[$key] = Carbon::parse($attributes[$key]);
		}

		$model = parent::newFromBuilder($attributes, $connection);

		return $model;
	}

}
