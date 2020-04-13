<?php

namespace Stylemix\Base\Attributes;

use Spatie\QueryBuilder\AllowedFilter;

/**
 * @property mixed $source Enum options source
 */
class Enum extends Text
{

	/**
	 * @inheritDoc
	 */
	public function applyFilter($filters)
	{
		$filters->push(AllowedFilter::exact($this->name));
	}

	/**
	 * Set source for options
	 *
	 * @param array|string $source
	 *
	 * @return $this
	 */
	public function source($source)
	{
		if (is_string($source) && class_exists($source)) {
			$this->source = $source::choices();
		}
		else {
			$this->source = $source;
		}

		return $this;
	}

	public function getSelectOptions()
	{
		$options = collect($this->source)->map(function ($label, $value) {
			return compact('label', 'value');
		});

		return $options->values()->all();
	}

}
