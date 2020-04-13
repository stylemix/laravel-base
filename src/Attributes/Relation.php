<?php

namespace Stylemix\Base\Attributes;

use Exception;
use Spatie\QueryBuilder\AllowedFilter;
use Stylemix\Base\Contracts\Filterable;
use Stylemix\Base\Entity;
use Stylemix\Base\QueryBuilder\NumberFilter;

/**
 * @property string $relation Related entity
 * @property boolean $castInteger Cast key as integer
 * @method $this excludeIncluding(bool $exclude = true) Exclude attribute from allowed includes
 * @property boolean $excludeIncluding Whether attribute is excluded from allowed includes
 */
class Relation extends BaseAttribute implements Filterable
{

	protected $queryBuilder;

	protected $where = [];

	/** @var string Related model primary key name */
	protected $otherKey;

	public function __construct(string $name, $relation = null, $foreignKey = null, $otherKey = 'id')
	{
		parent::__construct($name);

		$this->relation     = $relation ?? $name;
		$this->fillableName = $foreignKey ?? $name . '_id';
		$this->otherKey     = $otherKey;
	}

	/**
	 * Adds attribute casts
	 *
	 * @param \Illuminate\Support\Collection $casts
	 */
	public function applyCasts($casts)
	{
		if ($this->castInteger) {
			$casts->put($this->fillableName, 'integer');
		}
	}

	/**
	 * @inheritdoc
	 */
	public function isValueEmpty($value)
	{
		return trim($value) === '';
	}

	/**
	 * @inheritDoc
	 */
	public function applyFilter($filters)
	{
		$filters->push(AllowedFilter::custom($this->fillableName, new NumberFilter()));
	}

	public function getQueryBuilder(Entity $entity)
	{
		return $this->getEloquentRelation($entity)->getQuery();
	}

	public function getOtherKey()
	{
		return $this->otherKey;
	}

	/**
	 * Get eloquent relation for the model
	 *
	 * @param \Stylemix\Base\Entity $entity
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\Relation
	 * @throws \Exception
	 */
	protected function getEloquentRelation(Entity $entity)
	{
		if (!method_exists($entity, $this->name)) {
			throw new Exception(sprintf('Relation for attribute "%s" is not defined in entity model', $this->name));
		}

		/** @var \Illuminate\Database\Eloquent\Relations\Relation $relation */
		$relation = null;
		\Illuminate\Database\Eloquent\Relations\Relation::noConstraints(function () use ($entity, &$relation) {
			$relation = $entity->{$this->name}();
		});

		if (!$relation instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
			throw new Exception(sprintf('Relation method for attribute "%s" does not return relation object', $this->name));
		}

		return $relation;
	}

}
