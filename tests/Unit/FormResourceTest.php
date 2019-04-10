<?php

namespace Stylemix\Base\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Stylemix\Base\Tests\Dummy\DummyField;
use Stylemix\Base\Tests\Dummy\DummyForm;
use Stylemix\Base\Tests\Dummy\DummyModel;

class FormResourceTest extends TestCase
{

	public function testFill()
	{
		$data = [
			'field1' => 'value1',
			'field2' => 'value2',
		];

		$request = Request::create('/', 'POST', $data);

		$form = $this->makeForm();

		$model = new DummyModel();
		$form->fill($model, $request);
		$this->assertEquals($data, $model->getAttributes());

		$model = new DummyModel();
		$form->fillOnly($model, ['field1'], $request);
		$this->assertEquals(Arr::only($data, 'field1'), $model->getAttributes());

		$model = new DummyModel();
		$form->fillExcept($model, ['field1'], $request);
		$this->assertEquals(Arr::except($data, 'field1'), $model->getAttributes());
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

		$this->assertEquals($data, $model->getAttributes());


		$data = [
			'field1' => 'value1',
			'field2' => null,
		];

		$request = Request::create('/', 'PUT', $data);

		$form = $this->makeForm();
		$model = new DummyModel();
		$form->fill($model, $request);

		$this->assertEquals($data, $model->getAttributes());
	}

	/**
	 * @return \Stylemix\Base\Tests\Dummy\DummyForm
	 */
	protected function makeForm()
	{
		return DummyForm::make()
			->setTestFields([
				DummyField::make('field1'),
				DummyField::make('field2'),
			]);
	}
}
