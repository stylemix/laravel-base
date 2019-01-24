<?php

namespace Stylemix\Base\Fields;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class Number extends Input
{
	public $type = 'number';

	public function getRules()
	{
		return array_merge(parent::getRules(), ['numeric']);
	}

	/**
	 * @inheritdoc
	 */
	protected function sanitizeRequestInput($value)
	{
		return strpos($value, '.') === false ? intval($value) : floatval($value);
	}

}
