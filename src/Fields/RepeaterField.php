<?php

namespace Stylemix\Base\Fields;

use Illuminate\Support\Arr;

/**
 * @method static \Stylemix\Base\Fields\RepeaterField make(string $attribute, array|string $subFields)
 * @property \Stylemix\Base\Fields\Base[] $fields
 * @property \Stylemix\Base\Fields\Base $field
 */
class RepeaterField extends Base
{

	public $component = 'repeater-field';

	protected $typeRules = [
		'array',
	];

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
		$rules = $this->getNormalizedRules();

		if ($this->required) {
			array_unshift($rules, 'min:1');
			array_unshift($rules, 'required');
		}
		elseif ($this->nullable) {
			array_unshift($rules, 'nullable');
		}

		if ($this->fields) {
			foreach ($this->fields as $field) {
				$rules['*.' . $field->attribute] = $field->getRules();
			}
		}
		elseif ($this->field) {
			$rules['*'] = $this->field->getRules();
		}

		return $rules;
	}

}
