<?php

namespace Stylemix\Base\Tests\Unit\Fields;

use Stylemix\Base\Fields\NumberField;
use Stylemix\Base\Fields\RepeaterField;
use Stylemix\Base\Fields\TextField;
use Stylemix\Base\Tests\TestCase;

class RepeaterFieldTest extends TestCase
{

	public function testMultipleFalse()
	{
		$this->expectException(\BadMethodCallException::class);
		$this->makeField(TextField::make('text'))->multiple(false);
	}

	public function testResolvingSingleMode()
	{
		$field = $this->makeField(TextField::make('text'));

		$this->assertEquals([], $field->resolve([]));
		$this->assertEquals([], $field->resolve(['dummy' => null]));
		$this->assertEquals([], $field->resolve(['dummy' => []]));
		$this->assertEquals(['lorem', 'ipsum'], $field->resolve(['dummy' => ['lorem', 'ipsum']]));
	}

	public function testResolvingMultiMode()
	{
		$field = $this->makeField([
			TextField::make('text'),
			NumberField::make('number'),
		]);

		$this->assertEquals([], $field->resolve([]));
		$this->assertEquals([], $field->resolve(['dummy' => null]));
		$this->assertEquals([], $field->resolve(['dummy' => []]));

		$this->assertEquals(
			[['text' => 'lorem'], ['number' => 1], ['ipsum']],
			$field->resolve(['dummy' => [['text' => 'lorem'], ['number' => 1], 'ipsum']])
		);
	}

	public function testRules()
	{
		$rules = $this->makeField([
			TextField::make('text'),
			NumberField::make('number'),
		])
			->rules('min:3')
			->getRules();

		$this->assertEquals([
			'nullable',
			'array',
			'min:3',
			'*.text' => ['nullable', 'string'],
			'*.number' => ['nullable', 'numeric'],
		], $rules);


		$rules = $this->makeField(TextField::make('text'))->getRules();

		$this->assertEquals([
			'nullable',
			'array',
			'*' => ['nullable', 'string'],
		], $rules);
	}

	protected function makeField($subFields, $attribute = 'dummy')
	{
		return RepeaterField::make($attribute, $subFields);
	}
}
