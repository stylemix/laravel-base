<?php

namespace Stylemix\Base\Fields;

use Carbon\Carbon;

class DatetimeField extends TextField
{

	public $type = 'datetime-local';

	public function getRules()
	{
		return array_merge(parent::getRules(), ['date']);
	}

	protected function sanitizeResolvedValue($value)
	{
		if ($value instanceof Carbon) {
			$value = $value->format('Y-m-d\TH:i:s');
		}

		return $value;
	}

}
