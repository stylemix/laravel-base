<?php

namespace Stylemix\Base\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\Filters\Filter;

class NumberFilter implements Filter
{

	public function __invoke(Builder $query, $value, string $property) : Builder
	{
		if ($this->isRangeRequest($value)) {
			$operators = [
				'gt' => '>',
				'gte' => '>=',
				'lt' => '<',
				'lte' => '<=',
			];

			foreach ($operators as $key => $operator) {
				if (isset($value[$key]) && trim($value[$key]) !== '') {
					$query->where($property, $operator, $this->parseValue($value[$key]));
				}
			}

			return $query;
		}

		if (is_array($value)) {
			return $query->whereIn($property, $this->parseValue($value));
		}

		return $query->where($property, '=', $this->parseValue($value));
	}

	protected function parseValue($value)
	{
		return $value;
	}

	protected function isRangeRequest($value)
	{
		if (!is_array($value) || !Arr::isAssoc($value)) {
			return false;
		}

		foreach (['gt', 'gte', 'lt', 'lte'] as $key) {
			if (isset($value[$key])) {
				return true;
			}
		}

		return false;
	}
}
