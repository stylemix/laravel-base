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

	public function saving($data, $model)
	{
		if ($this->generateFrom instanceof Closure) {
			$model[$this->fillableName] = call_user_func($this->generateFrom, $data, $model);
		}
		else {
			$model[$this->fillableName] = Str::slug($data->get($this->generateFrom) ?? Str::random(8));
		}
	}

}
