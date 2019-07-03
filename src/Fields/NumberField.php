<?php

namespace Stylemix\Base\Fields;

/**
 * Class Number
 *
 * @property boolean $integer
 * @method $this integer($isInteger = true)
 * @property boolean $range
 * @method $this range($isRange = true)
 */
class NumberField extends Base
{

	public $component = 'number-field';

	protected function getTypeRules() : array
	{
		return [$this->integer ? 'integer' : 'numeric'];
	}

	/**
	 * @inheritdoc
	 */
	protected function sanitizeRequestInput($value)
	{
		return $this->integer ? intval($value) : floatval($value);
	}

	/**
	 * @inheritdoc
	 */
	protected function sanitizeResolvedValue($value)
	{
		return $this->integer ? intval($value) : floatval($value);
	}

}
