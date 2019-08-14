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

	protected $multiField = false;

	public function __construct($attribute, $subFields)
	{
		parent::__construct($attribute);

		$this->multiple = true;

		if (is_array($subFields)) {
			$this->fields = $subFields;
			$this->multiField = true;
		}
		else {
			$this->field = $subFields;
		}
	}

	protected function sanitizeRequestInput($value)
	{
		return $this->multiField ? Arr::wrap($value) : $value;
	}

	protected function sanitizeResolvedValue($value)
	{
		return $this->multiField ? Arr::wrap($value) : $value;
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

	public function resolve($data, $attribute = null)
	{
		$resolved = parent::resolve($data, $attribute);

		return !$resolved ? [] : $resolved;
	}

	public function multiple($multiple)
	{
		if (!$multiple) {
			throw new \BadMethodCallException('Turning off multiple flag for RepeaterField is denied.');
		}
	}

}
