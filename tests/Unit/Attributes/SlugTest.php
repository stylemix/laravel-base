<?php

namespace Stylemix\Base\Tests\Unit\Attributes;

use Stylemix\Base\Tests\Dummy\DummyEntity;
use Stylemix\Base\Tests\TestCase;

class SlugTest extends TestCase
{

	public function testGenerateFrom()
	{
		/** @var \Stylemix\Base\Attributes\Slug $attribute */
		$attribute = DummyEntity::getAttributeDefinitions()->find('slug');
		$model = new DummyEntity();
		$model->title = 'Dummy Title';
		$attribute->saving(collect($model->getAttributes()), $model);
		$this->assertEquals('dummy-title', $model->slug);
	}
}
