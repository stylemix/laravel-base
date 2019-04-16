<?php

namespace Stylemix\Base\Tests\Unit\Fields;

use Illuminate\Support\Carbon;
use Stylemix\Base\Fields\DatetimeField;
use Stylemix\Base\Tests\TestCase;

class DatetimeFieldTest extends TestCase
{

	public function testResolve()
	{
		$field = $this->makeField();
		$value = Carbon::create(2019, 1, 1, 18, 0, 0);
		$this->assertEquals('2019-01-01T18:00:00', $field->resolve(['dummy' => $value]));
	}

	/**
	 * @param string $attribute
	 *
	 * @return \Stylemix\Base\Fields\DatetimeField
	 */
	protected function makeField($attribute = 'dummy')
	{
		return DatetimeField::make($attribute);
	}
}
