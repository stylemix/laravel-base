<?php

namespace Stylemix\Base\Attributes;

use Spatie\QueryBuilder\Filter;
use Stylemix\Base\Contracts\Filterable;
use Stylemix\Base\Contracts\Sortable;
use Stylemix\Base\QueryBuilder\DatetimeFilter;

class Datetime extends BaseAttribute implements Sortable, Filterable
{

	/**
	 * Adds attribute casts
	 *
	 * @param \Illuminate\Support\Collection $casts
	 */
	public function applyCasts($casts)
	{
		$casts->put($this->name, 'datetime');
	}

	/**
	 * @inheritDoc
	 */
	public function applyFilter($filters)
	{
		$filters->push(Filter::custom($this->name, DatetimeFilter::class));
	}
}
