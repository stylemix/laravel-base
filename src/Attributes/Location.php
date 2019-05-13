<?php

namespace Stylemix\Base\Attributes;

use Stylemix\Base\Contracts\Filterable;
use Stylemix\Base\Contracts\Sortable;

class Location extends BaseAttribute
{

	/**
	 * @inheritdoc
	 */
	public function __construct(string $name = null)
	{
		$name = $name ?? 'location';
		parent::__construct($name);
	}
}
