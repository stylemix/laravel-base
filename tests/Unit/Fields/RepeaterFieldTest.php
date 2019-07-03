<?php

namespace Stylemix\Base\Tests\Unit\Fields;

use Stylemix\Base\Fields\NumberField;
use Stylemix\Base\Fields\RepeaterField;
use Stylemix\Base\Fields\TextField;
use Stylemix\Base\Tests\TestCase;

class RepeaterFieldTest extends TestCase
{

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
