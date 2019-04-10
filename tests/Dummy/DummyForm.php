<?php

namespace Stylemix\Base\Tests\Dummy;

use Stylemix\Base\FormResource;

class DummyForm extends FormResource
{

	protected $testFields;

	/**
	 * List of field definitions defined by descendant class
	 *
	 * @return \Stylemix\Base\Fields\Base[]
	 */
	public function fields()
	{
		return $this->testFields;
	}

	/**
	 * @param mixed $testFields
	 *
	 * @return DummyForm
	 */
	public function setTestFields($testFields)
	{
		$this->testFields = $testFields;

		return $this;
	}
}
