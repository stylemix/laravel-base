<?php

namespace Stylemix\Base\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Stylemix\Base\FormBuilder;

/**
 * @method static register(string $class, $builder) Register a fields builder function for attribute class
 * @method static registerByName(string $name, $builder) Register a fields builder function by attribute name
 * @method static extend(string $attribute, callable $callback) Add function that alters field by attribute name
 * @method static extendAll(callable $callback) Add function that performs some modifications to all fields
 * @method static Collection forAttributes($attributes) Generate form fields for given attributes
 */
class EntityForm extends Facade
{

	protected static function getFacadeAccessor()
	{
		return FormBuilder::class;
	}
}
