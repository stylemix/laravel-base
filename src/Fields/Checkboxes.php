<?php

namespace Stylemix\Base\Fields;

/**
 * @property array $options
 * @method $this options($options) Set checkbox options
 */
class Checkboxes extends Base
{

	public $component = 'checkboxes-field';

	protected $defaults = [
		'checkboxLayout' => null,
	];

	protected function sanitizeRequestInput($value)
	{
		return array_wrap($value);
	}

}
