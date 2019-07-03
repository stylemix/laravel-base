<?php

namespace Stylemix\Base\Fields;

use Illuminate\Support\Str;

/**
 * Class Text
 *
 * @method $this typeColor()
 * @method $this typeDate()
 * @method $this typeEmail()
 * @method $this typeMonth()
 * @method $this typeTel()
 * @method $this typeText()
 * @method $this typeTime()
 * @method $this typeUrl()
 */
class TextField extends Base
{

	protected static $availableTypes = [
		'color',
		'date',
		'email',
		'month',
		'password',
		'search',
		'tel',
		'text',
		'time',
		'week',
		'url',
	];

	public $component = 'text-field';

	public $type = 'text';

	protected $typeRules = [
		'string',
	];

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
		if (Str::startsWith($method, 'type')) {
			$this->fillType($method);
			return $this;
		}

		return parent::__call($method, $parameters);
	}

	/**
	 * @param $method
	 *
	 * @throws \Exception
	 */
	protected function fillType($method)
	{
		$type = strtolower(str_replace('type', '', $method));

		if (!in_array($type, static::$availableTypes)) {
			throw new \Exception(sprintf('Input type {%s} is not supported for text field. Use special dedicated field types.', $type));
		}

		$this->type = $type;
	}

}
