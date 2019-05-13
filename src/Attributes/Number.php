<?php

namespace Stylemix\Base\Attributes;

use Spatie\QueryBuilder\Filter;
use Stylemix\Base\Contracts\Filterable;
use Stylemix\Base\Contracts\Sortable;
use Stylemix\Base\QueryBuilder\NumberFilter;

/**
 * @property boolean $integer
 * @method  $this integer() Accept only integer numbers
 */
class Number extends BaseAttribute implements Filterable, Sortable
{

	/**
	 * Adds attribute casts
	 *
	 * @param \Illuminate\Support\Collection $casts
	 */
	public function applyCasts($casts)
	{
		$casts->put($this->name, $this->integer ? 'integer' : 'float');
	}

	public function isValueEmpty($value)
	{
		return trim($value) === '';
	}

	/**
	 * @inheritDoc
	 */
	public function applyFilter($filters)
	{
		$filters->push(Filter::custom($this->name, NumberFilter::class));
	}
}
