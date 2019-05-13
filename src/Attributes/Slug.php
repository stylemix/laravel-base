<?php

namespace Stylemix\Base\Attributes;

use Closure;
use Illuminate\Support\Str;

/**
 * @property string $generateFrom
 * @method $this generateFrom(string|callable $from) Set attribute name to generate from or callback function
 */
class Slug extends Text
{

	public function __construct(string $name = null)
	{
		$name = $name ?? 'slug';
		$this->generateFrom = 'title';

		parent::__construct($name);
	}

	public function applyDefaultValue($attributes)
	{
		if ($this->generateFrom instanceof Closure) {
			return call_user_func($this->generateFrom, $attributes);
		}
		else {
			return Str::slug($attributes->get($this->generateFrom) ?? Str::random(8));
		}
	}

}
