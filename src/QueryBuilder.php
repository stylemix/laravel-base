<?php

namespace Stylemix\Base;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Stylemix\Base\Attributes\Relation;
use Stylemix\Base\QueryBuilder\SpatieQueryBuilder;

class QueryBuilder
{

	/** @var \Stylemix\Base\AttributeCollection */
	protected $attributes;

	/** @var \Stylemix\Base\QueryBuilder\SpatieQueryBuilder */
	protected $builder;

	protected $allowedFilters = [];

	protected $allowedSorts = [];

	protected $allowedIncludes = [];

	public function __construct($builder)
	{
		$this->builder = $builder;
	}

	public function getAllowedFilters()
	{
		$filters = collect();

		$this->attributes
			->implementsFiltering()
			->where('excludeFilter', '=', false)
			->each->applyFilter($filters);

		return $filters->all();
	}

	public function getAllowedSearchables()
	{
		return $this->attributes
			->implementsSearching()
			->where('excludeSearch', '=', false)
			->pluck('name')->all();
	}

	public function getAllowedSorts()
	{
		$sorts = collect();

		$this->attributes
			->implementsSortable()
			->where('excludeSort', '=', false)
			->each->applySort($sorts);

		return $sorts->all();
	}

	public function getAllowedIncludes()
	{
		return $this->attributes
			->whereInstanceOf(Relation::class)
			->where('excludeIncluding', '=', false)
			->pluck('name')->all();
	}

	/**
	 * @return \Stylemix\Base\AttributeCollection
	 */
	public function getAttributes() : AttributeCollection
	{
		return $this->attributes;
	}

	/**
	 * @param \Stylemix\Base\AttributeCollection $attributes
	 *
	 * @return $this
	 */
	public function setAttributes(AttributeCollection $attributes)
	{
		$this->attributes = $attributes;

		return $this;
	}

	public function build()
	{
		$this->builder->allowedSorts(array_merge($this->allowedSorts, $this->getAllowedSorts()));
		$this->builder->allowedFilters(array_merge($this->allowedFilters, $this->getAllowedFilters()));
		$this->builder->allowedIncludes(array_merge($this->allowedIncludes, $this->getAllowedIncludes()));

		if ($query = $this->builder->getRequest()->get('query')) {
			$this->buildLikeQuery($query);
		}

		return $this->builder;
	}

	protected function buildLikeQuery($query)
	{
		$searchables = $this->getAllowedSearchables();

		if (!count($searchables)) {
			return;
		}

		$query = mb_strtolower($query, 'UTF8');
		$this->builder->where(function (Builder $builder) use ($searchables, $query) {
			foreach ($searchables as $searchable) {
				$builder->orWhereRaw($sql = "LOWER({$searchable}) LIKE ?", ["%{$query}%"]);
			}
		});
	}

	/**
	 * Create a new QueryBuilder for a request and model.
	 *
	 * @param string|\Illuminate\Database\Eloquent\Builder $baseQuery Model class or base query builder
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Stylemix\Base\QueryBuilder
	 */
	public static function for($baseQuery, ?Request $request = null)
	{
		if (is_string($baseQuery)) {
			$baseQuery = ($baseQuery)::query();
		}

		/** @var \Stylemix\Base\QueryBuilder\SpatieQueryBuilder $builder */
		$builder = SpatieQueryBuilder::for($baseQuery, $request);
		$instance = new static($builder);

		/** @var \Stylemix\Base\Entity $model */
		$model = $baseQuery->getModel();
		$instance->setAttributes($model::getAttributeDefinitions());

		return $instance;
	}

	/**
	 * @param array $allowedFilters
	 *
	 * @return QueryBuilder
	 */
	public function allowedFilters(array $allowedFilters) : self
	{
		$this->allowedFilters = $allowedFilters;

		return $this;
	}

	/**
	 * @param array $allowedSorts
	 *
	 * @return QueryBuilder
	 */
	public function allowedSorts(array $allowedSorts) : self
	{
		$this->allowedSorts = $allowedSorts;

		return $this;
	}

	/**
	 * @param array $allowedIncludes
	 *
	 * @return QueryBuilder
	 */
	public function allowedIncludes(array $allowedIncludes) : self
	{
		$this->allowedIncludes = $allowedIncludes;

		return $this;
	}
}
