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

}
