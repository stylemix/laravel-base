<?php

namespace Stylemix\Base\Tests\Unit\Fields;

use Stylemix\Base\Tests\Dummy\DummyField;
use Stylemix\Base\Tests\Dummy\DummyValidator;
use Stylemix\Base\Tests\TestCase;

class BaseTest extends TestCase
{

	public function testRules()
	{
		$field = $this->makeField()
			->rules('string');
		$this->assertEquals(['nullable', 'string'], $field->getRules());

		$field = $this->makeField()
			->rules(['string']);
		$this->assertEquals(['nullable', 'string'], $field->getRules());

		$field = $this->makeField()
			->rules(['string|min:3']);
		$this->assertEquals(['nullable', 'string', 'min:3'], $field->getRules());

		$field = $this->makeField()
			->rules([function () {}]);
		$this->assertEquals(['nullable', function () {}], $field->getRules());

		$validator = new DummyValidator();
		$field     = $this->makeField()->rules($validator);
		$this->assertEquals(['nullable', $validator], $field->getRules());
	}

	/**
	 * @return \Stylemix\Base\Tests\Dummy\DummyField
	 */
	protected function makeField()
	{
		return DummyField::make('dummy');
	}
}
