<?php

namespace Stylemix\Base\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Stylemix\Base\Fields\NumberField;
use Stylemix\Base\Fields\TextField;
use Stylemix\Base\Tests\Dummy\DummyField;
use Stylemix\Base\Tests\Dummy\DummyForm;
use Stylemix\Base\Tests\Dummy\DummyModel;

class FormResourceTest extends TestCase
{

	protected function setUp() : void
	{
		parent::setUp();
	}

	public function testToArray()
	{
		$request = Request::create('/');

		// no resource provided
		$resolved = $this->makeForm()->resolve($request);
		$this->assertSame([
			'text' => null,
			'number' => null,
			'field1' => null,
		], $resolved);

		// resource with values
		$resolved = $this->makeForm()
			->setResource(['field1' => 'val1'])
			->resolve($request);

		$this->assertSame([
			'field1' => 'val1',
			'text' => null,
			'number' => null,
		], $resolved);
	}

	public function testFill()
	{
		$data = [
			'text' => 'text value',
			'number' => 123.0,
			'field1' => 'value1',
		];

		$request = Request::create('/', 'POST', $data);

		$form = $this->makeForm();

		$model = new DummyModel();
		$form->fill($model, $request);
		$this->assertSame($data, $model->getAttributes());

		$model = new DummyModel();
		$form->fillOnly($model, ['field1'], $request);
		$this->assertSame(Arr::only($data, 'field1'), $model->getAttributes());

		$model = new DummyModel();
		$form->fillExcept($model, ['field1'], $request);
		$this->assertSame(Arr::except($data, 'field1'), $model->getAttributes());
	}

	public function testFillingUpdateRequest()
	{
		$data = [
			'field1' => 'value1',
		];

		$request = Request::create('/', 'PUT', $data);

		$form = $this->makeForm();
		$model = new DummyModel();
		$form->fill($model, $request);

		$this->assertSame($data, $model->getAttributes());


		$data = [
			'field1' => 'value1',
		];

		$request = Request::create('/', 'PUT', $data);

		$form = $this->makeForm();
		$model = new DummyModel();
		$form->fill($model, $request);

		$this->assertSame($data, $model->getAttributes());
	}

	public function testRules()
	{
		$request = Request::create('/');
		$rules = $this->makeForm()
			->setTestFields([
				TextField::make('text1')
					->required(),
				TextField::make('text2')
					->required()
					->multiple()
					->rules(Rule::unique('table')),
			])
			->rules($request);

		$this->assertEquals([
			'text1' => ['nullable', 'required', 'string'],
			'text2.*' => ['nullable', 'required', 'string', Rule::unique('table')],
			'text2' => ['array'],
		], $rules);
	}

	/**
	 * @return \Stylemix\Base\Tests\Dummy\DummyForm
	 */
	protected function makeForm()
	{
		return DummyForm::make()
			->setTestFields([
				TextField::make('text'),
				NumberField::make('number'),
				DummyField::make('field1'),
			]);
	}
}
