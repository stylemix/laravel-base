<?php

namespace Stylemix\Base\Attributes;

use Spatie\QueryBuilder\Filter;
use Stylemix\Base\Contracts\Filterable;
use Stylemix\Base\Contracts\Sortable;

class Boolean extends BaseAttribute implements Filterable, Sortable
{

	/**
	 * @inheritDoc
	 */
	public function applyCasts($casts)
	{
		$casts[$this->name] = 'boolean';
	}

	/**
	 * @inheritDoc
	 */
	public function applyFilter($filters)
	{
		$filters->push(Filter::exact($this->name));
	}

}
