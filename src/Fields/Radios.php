<?php

namespace Stylemix\Base\Fields;

/**
 * @property array $options
 * @method $this options($options) Set checkbox options
 */
class Radios extends Base
{

	public $component = 'radios-field';

	protected $defaults = [
		'radiosLayout' => null,
	];

}
