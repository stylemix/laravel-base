<?php

namespace Stylemix\Base\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Stylemix\Base\Fields\NumberField;
use Stylemix\Base\Fields\TextField;
use Stylemix\Base\Tests\Dummy\DummyField;
use Stylemix\Base\Tests\Dummy\DummyForm;
use Stylemix\Base\Tests\Dummy\DummyModel;

class FormResourceTest extends TestCase
{

	public function testToArray()
	{
		$request = Request::create('/');

		// no resource provided
		$resolved = $this->makeForm()->resolve($request);
		$this->assertSame([
			'text' => null,
			'number' => null,
			'field1' => null,
		], $resolved['data']);

		// resource with values
		$resolved = $this->makeForm()
			->setResource(['field1' => 'val1'])
			->resolve($request);

		$this->assertSame([
			'field1' => 'val1',
			'text' => null,
			'number' => null,
		], $resolved['data']);
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
