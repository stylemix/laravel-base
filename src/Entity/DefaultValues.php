<?php
namespace Stylemix\Base\Entity;

trait DefaultValues
{
	/**
	 * Resolved static values for each Entity class
	 *
	 * @var array
	 */
	protected static $defaultValues = [];

	protected static function bootDefaultValues()
	{
		// When testing application, resolved default values
		// should be reset for each test.
		static::$defaultValues = [];
	}

	protected function initializeDefaultValues()
	{
		$class = static::class;

		if (!isset(static::$defaultValues[$class])) {
			/** @var \Stylemix\Base\AttributeCollection|\Stylemix\Base\Attributes\BaseAttribute[] $attributes */
			$attributes = static::getAttributeDefinitions();

			$values = $this->getAttributes();
			// Get defaults only for those keys that are missing
			$attributes = $attributes->diffKeys($values);

			foreach ($attributes as $attribute) {
				$value = $attribute->applyDefaultValue($values);
				if ($value !== null) {
					$values[$attribute->name] = $value;
				}
			}

			static::$defaultValues[$class] = $values;
		}

		$values = static::$defaultValues[$class];
		$this->setRawAttributes($values, true);
	}
}
