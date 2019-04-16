<?php

namespace Stylemix\Base\Tests\Unit\Fields;

use Stylemix\Base\Fields\NumberField;
use Stylemix\Base\Tests\TestCase;

class NumberFieldTest extends TestCase
{

	public function testResolve()
	{
		$field = $this->makeField();
		$value = '12';
		$this->assertEquals(12, $field->resolve(['dummy' => $value]));
	}

	/**
	 * @param string $attribute
	 *
	 * @return \Stylemix\Base\Fields\NumberField
	 */
	protected function makeField($attribute = 'dummy')
	{
		return NumberField::make($attribute);
	}
}
