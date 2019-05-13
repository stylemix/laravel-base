<?php

namespace Stylemix\Base\Attributes;

use Stylemix\Base\Contracts\Filterable;
use Stylemix\Base\Contracts\Searchable;

/**
 * @property $editor Use editor in form
 * @method $this editor() Use editor in form
 */
class LongText extends BaseAttribute implements Searchable, Filterable
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
