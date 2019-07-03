<?php

namespace Stylemix\Base\Fields;

class EmailField extends TextField
{

	public $type = 'email';

	protected $typeRules = [
		'email'
	];

}
