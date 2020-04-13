<?php

namespace Stylemix\Base\Tests;

use Spatie\QueryBuilder\AllowedFilter;
use Stylemix\Base\AttributeCollection;
use Stylemix\Base\QueryBuilder;
use Stylemix\Base\QueryBuilder\DatetimeFilter;
use Stylemix\Base\QueryBuilder\NumberFilter;
use Stylemix\Base\Tests\Dummy\DummyEntity;

class QueryBuilderTest extends TestCase
{

	public function testAllowedSorts()
	{
		$builder = QueryBuilder::for(DummyEntity::class);

		$this->assertEquals([
			'text',
			'enum',
			'slug',
			'number',
			'id',
			'boolean',
			'datetime',
		], $builder->getAllowedSorts());
	}

	public function testAllowedFilters()
	{
		$builder = QueryBuilder::for(DummyEntity::class);

		$this->assertEquals([
			'text',
			AllowedFilter::exact('enum'),
			'slug',
			'long_text',
			AllowedFilter::custom('number', new NumberFilter),
			AllowedFilter::custom('id', new NumberFilter),
			AllowedFilter::exact('boolean'),
			AllowedFilter::custom('datetime', new DatetimeFilter),
			AllowedFilter::custom('relation_id', new NumberFilter),
		], $builder->getAllowedFilters());
	}

	public function testAllowedIncludes()
	{
		$builder = QueryBuilder::for(DummyEntity::class);

		$this->assertEquals([
			'relation',
		], $builder->getAllowedIncludes());
	}

	protected function attributeCollection()
	{
		return new AttributeCollection();
	}
}
