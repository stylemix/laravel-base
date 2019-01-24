<?php

namespace Stylemix\Base\Fields;

class Checkbox extends Base
{

	public $component = 'checkbox-field';

	/**
	 * @inheritdoc
	 */
	protected function sanitizeRequestInput($value)
	{
		return boolval($value);
	}

}
