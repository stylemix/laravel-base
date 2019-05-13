<?php

namespace Stylemix\Base\Attributes;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

/**
 * @property string  $label Label for attribute
 * @property string  $placeholder Placeholder for attribute
 * @property boolean $multiple If attribute has multiple values
 * @method $this multiple() Allow this attribute to have multiple values
 * @property boolean $required Whether an attribute's field should be required
 * @method $this required() Make this attribute required
 * @property boolean $excludeSort Whether attribute is excluded from sort
 * @method $this excludeSort(bool $exclude = true) Exclude attribute from allowed sorts
 * @property boolean $excludeFilter Whether attribute is excluded from allowed filters
 * @method $this excludeFilter(bool $exclude = true) Whether attribute is excluded from allowed filters
 * @property boolean $excludeSearching Whether attribute is excluded from searching
 * @method $this excludeSearching(bool $exclude = true) Whether attribute is excluded from searching
 * @property mixed  $defaultValue Default value for attribute. Can be function that accepts current model attributes.
 * @method $this defaultValue(mixed|callable $value) Default value for attribute. Can be function that accepts current model attributes.
 * @method $this fillable() Allow attribute to be filled by forms
 * @method $this search(array $config) Search configuration
 */
abstract class BaseAttribute extends Fluent
{

	/**
	 * @var string Attribute type
	 */
	public $type;

	/**
	 * @var string Attribute name
	 */
	public $name;

	/**
	 * @var string Attribute name for filling
	 */
	public $fillableName;

	/**
	 * Base constructor.
	 *
	 * @param string $name Attribute name
	 */
	public function __construct($name)
	{
		$this->type = $this->type ?: Str::snake(class_basename($this));
		$this->name = $name;
		$this->fillableName = $name;

		parent::__construct([]);
	}

	/**
	 * Adds fillable properties for attribute
	 *
	 * @param \Illuminate\Support\Collection $fillable
	 */
	public function applyFillable($fillable)
	{
		$fillable->push($this->fillableName);
	}

	/**
	 * Attribute key that attribute is responsible for fill
	 *
	 * @return string
	 */
	public function fills()
	{
		return $this->fillableName;
	}

	/**
	 * Checks whether the value for attribute should be marked as empty and removed.
	 * Passed value is always taken from fillable key.
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function isValueEmpty($value)
	{
		return empty($value);
	}

	/**
	 * Attribute keys that attribute is responsible for sort
	 *
	 * @return array
	 */
	public function sorts()
	{
		return [$this->name, $this->fillableName];
	}

	/**
	 * Adds attribute casts
	 *
	 * @param \Illuminate\Support\Collection $casts
	 */
	public function applyCasts($casts)
	{

	}

	/**
	 * Manipulate data when calling toArray
	 *
	 * @param \Illuminate\Support\Collection $data
	 * @param \Stylemix\Base\Entity $model
	 */
	public function applyArrayData($data, $model)
	{

	}

	/**
	 * Add attribute name to allowed sorts
	 *
	 * @param \Illuminate\Support\Collection $sort
	 */
	public function applySort($sort) : void
	{
		$sort->push($this->name);
	}

	/**
	 * Actions before model is saved
	 *
	 * @param \Illuminate\Support\Collection $data
	 * @param \Stylemix\Base\Entity $model
	 */
	public function saving($data, $model)
	{

	}

	/**
	 * Actions after model is saved
	 *
	 * @param \Stylemix\Base\Entity $model
	 */
	public function saved($model)
	{

	}

	/**
	 * Actions before model is deleted
	 *
	 * @param \Stylemix\Base\Entity $model
	 */
	public function deleting($model)
	{

	}

	/**
	 * Returns default value for the attribute
	 *
	 * @param \Illuminate\Support\Collection $attributes
	 *
	 * @return mixed
	 */
	public function applyDefaultValue($attributes)
	{
		$defaultValue = $this->defaultValue;

		return $defaultValue instanceof \Closure ? $defaultValue($attributes) : $defaultValue;
	}

	/**
	 * @inheritdoc
	 */
	public function toArray()
	{
		return array_merge(['name' => $this->name, 'type' => $this->type], parent::toArray());
	}

	/**
	 * Creates new instance of the attribute
	 *
	 * @param string $name Attribute name
	 * @param mixed ...$arguments
	 *
	 * @return static
	 */
	public static function make($name, ...$arguments)
	{
		return new static($name, ...$arguments);
	}

	/**
	 * Get label from translations by attribute name. Defaults to studly case from name
	 *
	 * @return string
	 */
	public function getFieldLabel()
	{
		return Arr::get(trans('attributes'), $this->name . '.label', function () {
			return Str::title(str_replace('_', ' ', Str::snake($this->name)));
		});
	}

	/**
	 * Get label from translations by attribute name. Defaults to studly case from name
	 *
	 * @return string
	 */
	public function getFieldPlaceholder()
	{
		return Arr::get(trans('attributes'), $this->name . '.placeholder');
	}

	/**
	 * Get label from translations by attribute name. Defaults to studly case from name
	 *
	 * @return string
	 */
	public function getFieldHelp()
	{
		return Arr::get(trans('attributes'), $this->name . '.help');
	}

	/**
	 * If is value is callable, calls it with additional arguments or returns as is
	 *
	 * @param mixed $value
	 * @param mixed ...$arguments
	 *
	 * @return mixed
	 */
	protected function evaluate($value, ...$arguments)
	{
		return $value instanceof \Closure ? $value($this, ...$arguments) : $value;
	}

}
