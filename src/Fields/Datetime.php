<?php

namespace Stylemix\Base\Fields;

class Datetime extends Input
{

	public $type = 'datetime-local';

	public function getRules()
	{
		return array_merge(parent::getRules(), ['date']);
	}

}
