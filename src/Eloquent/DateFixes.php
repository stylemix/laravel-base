<?php

namespace Stylemix\Base\Eloquent;

use Illuminate\Support\Carbon;

trait DateFixes
{

	/**
	 * Return a timestamp as DateTime object.
	 *
	 * @param  mixed  $value
	 * @return \Illuminate\Support\Carbon
	 */
	protected function asDateTime($value)
	{
		if (is_string($value) && !is_numeric($value)) {
			return Carbon::parse($value);
		}

		return parent::asDateTime($value);
	}
}
