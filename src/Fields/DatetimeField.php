<?php

namespace Stylemix\Base\Fields;

use Carbon\Carbon;
use DateTime;

class DatetimeField extends Base
{

	public $component = 'datetime-field';

	protected $typeRules = [
		'date'
	];

	protected function sanitizeResolvedValue($value)
	{
		if ($value instanceof Carbon) {
			$value = $value->format(DateTime::ISO8601);
		}

		return $value;
	}

}
