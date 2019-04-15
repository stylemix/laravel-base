<?php

namespace Stylemix\Base\Fields;

use Illuminate\Support\Arr;

/**
 * @property array $options
 * @method $this options($options) Set checkbox options
 */
class CheckboxesField extends Base
{

	public $component = 'checkboxes-field';

	protected $defaults = [
		'checkboxLayout' => null,
	];

	protected function sanitizeRequestInput($value)
	{
		return Arr::wrap($value);
	}

}
