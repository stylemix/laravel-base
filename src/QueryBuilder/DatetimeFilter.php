<?php

namespace Stylemix\Base\QueryBuilder;

use Carbon\Carbon;

class DatetimeFilter extends NumberFilter
{

	protected function parseValue($value)
	{
		return Carbon::parse($value)->toDateTimeString();
	}

}
