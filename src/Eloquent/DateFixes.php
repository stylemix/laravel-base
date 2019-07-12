<?php

namespace Stylemix\Base\Eloquent;

use DateTime;
use DateTimeInterface;
use Illuminate\Support\Carbon;

trait DateFixes
{

	/**
	 * The storage format of the model's date columns.
	 *
	 * @var string
	 */
	protected $arrayDateFormat;

	/**
	 * Return a timestamp as DateTime object.
	 *
	 * @param  mixed  $value
	 * @return \Illuminate\Support\Carbon
	 */
	protected function asDateTime($value)
	{
		// Allow to pass date in any format parsable for Carbon
		if (is_string($value) && !is_numeric($value)) {
			// In order to store timestamps in database in equal format
			// we will convert it to application timezone
			return Carbon::parse($value)->setTimezone(config('app.timezone'));
		}

		return parent::asDateTime($value);
	}

	/**
	 * Prepare a date for array / JSON serialization.
	 *
	 * @param  \DateTimeInterface  $date
	 * @return string
	 */
	protected function serializeDate(DateTimeInterface $date)
	{
		return $date->format($this->getArrayDateFormat());
	}

	/**
	 * Get the format for database stored dates.
	 *
	 * @return string
	 */
	protected function getArrayDateFormat()
	{
		return $this->arrayDateFormat ?: DateTime::ISO8601;
	}
}
