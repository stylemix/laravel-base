<?php

namespace Stylemix\Base\Fields;

use Illuminate\Support\Arr;

/**
 * @method static \Stylemix\Base\Fields\RepeaterField make(string $attribute, array|string $subFields)
 */
class RepeaterField extends Base
{

	public $component = 'repeater-field';

	public function __construct($attribute, $subFields)
	{
		parent::__construct($attribute);

		$this->multiple = true;

		if (is_array($subFields)) {
			$this->fields = $subFields;
		}
		else {
			$this->field = $subFields;
		}
	}

	protected function sanitizeRequestInput($value)
	{
		return Arr::wrap($value);
	}

	protected function sanitizeResolvedValue($value)
	{
		return Arr::wrap($value);
	}

	public function getRules()
	{
		return array_merge(parent::getRules(), ['array']);
	}

}
