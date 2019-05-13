<?php

namespace Stylemix\Base\QueryBuilder;

use Spatie\QueryBuilder\Exceptions\InvalidSortQuery;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilderBase;

class SpatieQueryBuilder extends SpatieQueryBuilderBase
{

	protected function guardAgainstUnknownSorts()
	{
		$sortNames = $this->request->sorts()->keys()->map(function ($sort) {
			return ltrim($sort, '-');
		});

		$allowedSortNames = $this->allowedSorts->map->getProperty();

		$diff = $sortNames->diff($allowedSortNames);

		if ($diff->count()) {
			throw InvalidSortQuery::sortsNotAllowed($diff, $allowedSortNames);
		}
	}

	protected function parseSorts()
	{
		$sorts = $this->request->sorts();

		if ($sorts->isEmpty()) {
			optional($this->defaultSorts)->sort($this);
		}

		$this
			->filterDuplicates($sorts)
			->each(function ($direction, string $property) {
				$sort = $this->findSort($property);

				$sort->sort($this, $direction === 'desc');
			});
	}

	public function getRequest()
	{
		return $this->request;
	}
}
