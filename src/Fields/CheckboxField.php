<?php

namespace Stylemix\Base\Fields;

class CheckboxField extends Base
{

	public $component = 'checkbox-field';

	protected $typeRules = [
		'boolean'
	];

	/**
	 * @inheritdoc
	 */
	protected function sanitizeRequestInput($value)
	{
		return boolval($value);
	}

}
