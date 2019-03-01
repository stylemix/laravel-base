<?php

namespace Stylemix\Base\Fields;

class Textarea extends Base
{

	public $component = 'textarea-field';

	protected $defaults = [
		'cols' => null,
		'rows' => null,
		'maxlength' => null,
	];

}
