<?php

namespace Stylemix\Base\Tests\Feature;

use Stylemix\Base\Tests\Dummy\DummyEntity;
use Stylemix\Base\Tests\TestCase;

class EntityTest extends TestCase
{

	public function testDefaultValues()
	{
		$attributes = DummyEntity::getAttributeDefinitions();
		$defaults = [
			'text' => 'text_default',
			'enum' => 'enum_default',
			'long_text' => 'long_text_default',
			'number' => 123.4,
			'boolean' => true,
			'datetime' => now(),
		];
		foreach ($defaults as $name => $default) {
			$attributes[$name]->defaultValue($default);
		}

		$dummy = new DummyEntity();
		foreach ($defaults as $name => $default) {
			$this->assertEquals($default, $dummy->getAttribute($name));
		}
	}
}
