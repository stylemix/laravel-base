<?php

namespace Stylemix\Base\Attributes;

class Id extends Number
{

	public function __construct(string $name = null)
	{
		$name = $name ?? 'id';
		parent::__construct($name);
	}

	public function applyFillable($fillable)
	{
		//
	}

	/**
	 * @inheritDoc
	 */
	public static function make($name = 'id', ...$arguments)
	{
		return new static($name, ...$arguments);
	}
}
