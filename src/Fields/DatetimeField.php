<?php

namespace Stylemix\Base\Fields;

class DatetimeField extends TextField
{

	public $type = 'datetime-local';

	public function getRules()
	{
		return array_merge(parent::getRules(), ['date']);
	}

}
