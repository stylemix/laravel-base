<?php

namespace Stylemix\Base\Fields;

/**
 * Class Input
 *
 * @method $this typeColor()
 * @method $this typeDate()
 * @method $this typeEmail()
 * @method $this typeMonth()
 * @method $this typeRange()
 * @method $this typeTel()
 * @method $this typeText()
 * @method $this typeTime()
 * @method $this typeUrl()
 */
class Input extends Base
{
	protected static $availableTypes = [
		'color',
		'date',
		'email',
		'month',
		'range',
		'tel',
		'text',
		'time',
		'url',
	];

	public $component = 'text-field';

	public $type = 'text';

	protected $defaults = [
		'min' => null,
		'max' => null,
		'step' => null,
		'pattern' => null,
	];

	public function toArray()
	{
		return array_merge(parent::toArray(), [
			'type' => $this->type,
		]);
	}

	public function __call($method, $parameters)
	{
		if (starts_with($method, 'type')) {
			$this->fillType($method);
			return $this;
		}

		return parent::__call($method, $parameters);
	}

	protected function fillType($method)
	{
		$type = strtolower(str_replace('type', '', $method));

		if (in_array($type, static::$availableTypes)) {
			$this->type = $type;
		}
	}

}
