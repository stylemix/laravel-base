<?php

namespace Stylemix\Base\Tests\Unit\Fields;

use Stylemix\Base\Tests\Dummy\DummyField;
use Stylemix\Base\Tests\Dummy\DummyModel;
use Stylemix\Base\Tests\Dummy\DummyValidator;
use Stylemix\Base\Tests\TestCase;

class BaseTest extends TestCase
{

	public function testResolve()
	{
		// no resource
		$field = $this->makeField();
		$this->assertNull($field->resolve(null));

		// no resource for multiple
		$field = $this->makeField()->multiple();
		$this->assertEquals([], $field->resolve(null));

		// resource value
		$field = $this->makeField();
		$this->assertEquals('value1', $field->resolve(['dummy' => 'value1']));

		// resource nested value
		$field = $this->makeField('nested.dummy');
		$this->assertEquals('value1', $field->resolve(['nested' => ['dummy' => 'value1']]));

		// resource model value
		$field = $this->makeField();
		$model = new DummyModel();
		$model->forceFill(['dummy' => 'value1']);
		$this->assertEquals('value1', $field->resolve($model));
	}

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

		$field = $this->makeField()
			->multiple()
			->rules('min:3');
		$this->assertEquals(['array', '*' => ['nullable', 'min:3']], $field->getRules());
	}

	/**
	 * @param string $attribute
	 *
	 * @return \Stylemix\Base\Tests\Dummy\DummyField
	 */
	protected function makeField($attribute = 'dummy')
	{
		return DummyField::make($attribute);
	}
}
