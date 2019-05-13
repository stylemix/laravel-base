<?php

namespace Stylemix\Base\Attributes;

use Stylemix\Base\Contracts\Filterable;
use Stylemix\Base\Contracts\Searchable;
use Stylemix\Base\Contracts\Sortable;

class Text extends BaseAttribute implements Sortable, Searchable, Filterable
{

	/**
	 * Adds attribute casts
	 *
	 * @param \Illuminate\Support\Collection $casts
	 */
	public function applyCasts($casts)
	{
		$casts->put($this->name, 'string');
	}

	/**
	 * @inheritDoc
	 */
	public function applyFilter($filters)
	{
		$filters->push($this->name);
	}
}
