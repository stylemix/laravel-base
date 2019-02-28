<?php

namespace Stylemix\Base\Eloquent;

use Illuminate\Support\Str;
use Konekt\Enum\Enum;

trait CastsEnums
{

	/**
	 * @inheritdoc
	 */
	public function hasSetMutator($key)
	{
		return parent::hasSetMutator($key) || $this->isEnumAttribute($key);
	}

	protected function setMutatedAttributeValue($key, $value)
	{
		if (method_exists($this, 'set'.Str::studly($key).'Attribute')) {
			return $this->{'set' . Str::studly($key) . 'Attribute'}($value);
		}

		if ($value instanceof Enum) {
			$this->attributes[$key] = $value->value();
		}
		else {
			$class = $this->getEnumClass($key);
			$this->attributes[$key] = $class::create($value)->value();
		}

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function hasGetMutator($key)
	{
		return parent::hasGetMutator($key) || $this->isEnumAttribute($key);
	}

	/**
	 * @inheritdoc
	 */
	protected function mutateAttribute($key, $value)
	{
		$method = 'get' . Str::studly($key) . 'Attribute';

		if (method_exists($this, $method)) {
			return $this->{$method}($value);
		}

		if (is_null($value)) {
			return null;
		}

		$class = $this->getEnumClass($key);

		return $class::create($value);
	}

    /**
     * Returns whether the attribute was marked as enum
     *
     * @param $key
     *
     * @return bool
     */
    protected function isEnumAttribute($key)
    {
        return isset($this->enums[$key]);
    }

    /**
     * Returns the enum class. Supports 'FQCN\Class@method()' notation
     *
     * @param $key
     *
     * @return mixed
     */
    private function getEnumClass($key)
    {
        $result = $this->enums[$key];
        if (strpos($result, '@')) {
            $class  = str_before($result, '@');
            $method = str_after($result, '@');

            // If no namespace was set, prepend the Model's namespace to the
            // class that resolves the enum class. Prevent this behavior,
            // by setting the resolver class with a leading backslash
            if (class_basename($class) == $class) {
                $class =
                    str_replace_last(
                        class_basename(get_class($this)),
                        $class,
                        self::class
                    );
            }

            $result = $class::$method();
        }

        return $result;
    }
}
