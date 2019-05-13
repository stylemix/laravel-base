<?php

namespace Stylemix\Base\Tests;

use Spatie\QueryBuilder\Filter;
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
			'enum',
			'slug',
			'long_text',
			Filter::custom('number', NumberFilter::class),
			Filter::custom('id', NumberFilter::class),
			Filter::exact('boolean'),
			Filter::custom('datetime', DatetimeFilter::class),
			Filter::custom('relation_id', NumberFilter::class),
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
