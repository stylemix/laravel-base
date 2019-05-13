<?php

namespace Stylemix\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Plank\Mediable\Mediable;
use Stylemix\Base\Eloquent\CastsEnums;
use Stylemix\Base\Eloquent\DateFixes;
use Stylemix\Base\Contracts\Mutatable;

abstract class Entity extends Model
{

	use DateFixes, CastsEnums, Mediable;

	/**
	 * @var \Stylemix\Base\AttributeCollection[]
	 */
	protected static $attributeDefinitions = [];

	protected static $resolvedFillable;

	protected static $resolvedCasts;

	protected $dateFormat = 'Y-m-d\TH:i:s';

	/**
	 * @var bool Force reloading attachments after changes
	 */
	protected $rehydrates_media = true;

	/**
	 * @inheritdoc
	 */
	public function getFillable()
	{
		return array_merge($this->fillable, static::$resolvedFillable[static::class]);
	}

	/**
	 * @inheritdoc
	 */
	public function getCasts()
	{
		return parent::getCasts() + static::$resolvedCasts[static::class];
	}

	/**
	 * @inheritdoc
	 */
	public function getAttribute($key)
	{
		if ($this->hasGetMutator($key) || method_exists(self::class, $key)) {
			return parent::getAttribute($key);
		}

		$attribute = $this->getAttributeDefinitions()->find($key);

		if ($attribute instanceof Mutatable) {
			return $attribute->getterMutator($this, $key);
		}

		if ($attribute && $attribute->multiple) {
			return $this->castMultipleAttribute($key, $this->getAttributeFromArray($key));
		}

		return parent::getAttribute($key);
	}

	/**
	 * @inheritdoc
	 */
	public function setAttribute($key, $value)
	{
		if ($this->hasSetMutator($key)) {
			parent::setAttribute($key, $value);

			return;
		}

		/** @var \Stylemix\Base\Attribute\Base $attribute */
		$attribute = $this->getAttributeDefinitions()->keyByFills()->get($key);

		if (!$attribute) {
			parent::setAttribute($key, $value);

			return;
		}

		if ($attribute instanceof Mutatable) {
			$value = $attribute->setterMutator($this, $key, $value);
		}

		if ($attribute->multiple) {
			$value = collect(array_values(Arr::wrap($value)))->filter(function ($value) use ($attribute) {
				return !$attribute->isValueEmpty($value);
			})->all();

			$this->attributes[$key] = $value;
			return;
		}

		parent::setAttribute($key, $value);
	}

	/**
	 * @inheritdoc
	 */
	protected function addCastAttributesToArray(array $attributes, array $mutatedAttributes)
	{
		$multiple = $this->getAttributeDefinitions()->where('multiple', true);
		$multiple = array_merge($multiple->keys()->all(), $multiple->keyBy->fills()->keys()->all());

		$casted = parent::addCastAttributesToArray(Arr::except($attributes, $multiple), $mutatedAttributes);

		foreach (Arr::only($attributes, $multiple) as $key => $value) {
			$casted[$key] = $this->castMultipleAttribute($key, $value);
		}

		return $casted;
	}

	/**
	 * Get an attribute array of all arrayable relations.
	 *
	 * @return array
	 */
	protected function getArrayableRelations()
	{
		return Arr::except($this->getArrayableItems($this->relations), ['media']);
	}

	/**
	 * @inheritdoc
	 */
	public function toArray()
	{
		$array = parent::toArray();

		static::getAttributeDefinitions()->each->applyArrayData($array = collect($array), $this);

		return $array->all();
	}

	/**
	 * Wraps value to array and casts each value in array
	 *
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return array
	 */
	protected function castMultipleAttribute($key, $value)
	{
		$values = is_array($value) || $value instanceof \ArrayAccess ? $value : array_wrap($value);

		if ($this->hasCast($key) && !$this->isJsonCastable($key)) {
			foreach ($values as $i => $value) {
				$values[$i] = $this->castAttribute($key, $value);
			}
		}

		return $values;
	}

	public function search()
	{

	}

	/**
	 * Attribute definitions
	 * @return array
	 */
	abstract protected static function attributeDefinitions() : array;

	/**
	 * @inheritdoc
	 */
	protected static function boot()
	{
		// Method boot calls bootTraits that may bind event listeners to the model
		// Since Laravel creates new instance of the model, while binding listeners
		// it required all fillables and casts to be resolved first
		static::resolveFillable();
		static::resolveCasts();

		parent::boot();

		static::observe(AttributeListener::class);
	}

	/**
	 * Get resolved attribute definitions.
	 *
	 * @return \Stylemix\Base\AttributeCollection|\Stylemix\Base\Attributes\BaseAttribute[]
	 */
	public static function getAttributeDefinitions()
	{
		if (!isset(static::$attributeDefinitions[static::class])) {
			$definitions = static::attributeDefinitions();

			static::$attributeDefinitions[static::class] = (new AttributeCollection($definitions))
				->keyBy('name');
		}

		return static::$attributeDefinitions[static::class];
	}

	/**
	 * Resolve and cache model fillable
	 */
	protected static function resolveFillable()
	{
		static::getAttributeDefinitions()->each->applyFillable($fillable = collect());
		static::$resolvedFillable[static::class] = $fillable->all();
	}

	/**
	 * Resolve and cache model casts
	 */
	protected static function resolveCasts()
	{
		static::getAttributeDefinitions()->each->applyCasts($casts = collect());

		// Remove casts for all multiple attributes
		$multiple = static::getAttributeDefinitions()
			->where('multiple', true)
			->pluck('fillableName');

		$casts = $casts->except($multiple);

		// TODO: implement casts to multiple attributes with the same rules

		static::$resolvedCasts[static::class] = $casts->all();
	}

}
