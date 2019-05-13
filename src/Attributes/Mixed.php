<?php

namespace Stylemix\Base\Attributes;

use Stylemix\Base\Contracts\Searchable;

class Mixed extends BaseAttribute implements Searchable
{

	public function __construct($name)
	{
		parent::__construct($name);
		$this->excludeSearching();
	}
}
