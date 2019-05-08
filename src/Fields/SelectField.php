<?php

namespace Stylemix\Base\Fields;

/**
 * @property mixed $options Dropdown options
 * @method $this options(array $options) Set dropdown options
 */
class SelectField extends Base
{

	public $component = 'select-field';

	protected $defaults = [
		'options' => [],
	];

	/**
	 * Import options from Enum class
	 *
	 * @param string $class
	 *
	 * @return $this
	 */
	public function fromEnum($class)
	{
		$this->options = collect($class::choices())
			->map(function ($label, $value) {
				return compact('label', 'value');
			})
			->values()
			->all();

		return $this;
	}

}
