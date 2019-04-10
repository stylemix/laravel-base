<?php

namespace Stylemix\Base\Fields;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

/**
 * @property string   $attribute Attribute name
 * @property string   $component Component for Vue
 * @property string   $label     Label for attribute
 * @method $this label($label) Set label for field
 * @property string $placeholder Placeholder for field
 * @method $this placeholder($placeholder) Set placeholder for field
 * @property boolean  $required  Field value is required
 * @method $this required($value = true) Set as required
 * @property boolean  $multiple  Multiple mode
 * @method $this multiple($value = true) Set as multiple
 */
abstract class Base extends Fluent
{

	/**
	 * @var array|string Additional rules
	 */
	public $rules;

	/**
	 * Default values for fields properties
	 * @var array
	 */
	protected $defaults = [];

	/**
	 * @var mixed Currently resolving resource
	 */
	protected $resource = null;

	/**
	 * @var callable Callback for resolving value from given resource
	 */
	protected $resolveCallback;

	/**
	 * @var callable Callback when field resolved to array
	 */
	protected $arrayCallback;

	/**
	 * Base constructor.
	 *
	 * @param string $attribute Attribute name
	 */
	public function __construct($attribute)
	{
		parent::__construct([]);

		$defaults = $this->defaults;
		$defaults += [
			'placeholder' => null,
			'readonly' => null,
			'disabled' => null,
		];

		foreach ($defaults as $key => $value) {
			$this->offsetSet($key, $value);
		}

		$this->attribute = $attribute;
		$this->label     = $this->getLabel();
	}

	/**
	 * Set component name
	 *
	 * @param string $component
	 *
	 * @return $this
	 */
	public function component($component)
	{
		$this->component = $component;

		return $this;
	}

	/**
	 * Set field validation rules
	 *
	 * @param mixed $rules
	 *
	 * @return $this
	 */
	public function rules($rules)
	{
		$this->rules = $rules;

		return $this;
	}

	/**
	 * Get rules for the attribute
	 *
	 * @return array
	 */
	public function getRules()
	{
		$rules = Arr::wrap($this->rules);

		if (isset($rules[0]) && is_string($rules[0]) && Str::contains($rules[0], '|')) {
			$rules = explode('|', $rules[0]);
		}

		if ($this->required) {
			array_unshift($rules, 'required');
		}
		else {
			array_unshift($rules, 'nullable');
		}

		return $rules;
	}

	/**
	 * Hydrate the given attribute on the model based on the incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  object                   $model
	 * @param  string                   $requestAttribute
	 * @param  string                   $attribute
	 *
	 * @return void
	 */
	public function fill(Request $request, $model, $requestAttribute = null, $attribute = null)
	{
		$requestAttribute = $requestAttribute ?? $this->attribute;
		$attribute = $attribute ?? $this->attribute;

		if (isset($this->fillCallback)) {
			call_user_func(
				$this->fillCallback, $request, $model, $requestAttribute, $attribute
			);

			return;
		}

		if ($request->exists($requestAttribute)) {
			$this->fillAttributeFromRequest(
				$request, $requestAttribute, $model, $attribute
			);
		}
	}

	/**
	 * Resolve the field's value.
	 *
	 * @param  mixed       $resource
	 * @param  string|null $attribute
	 *
	 * @return mixed
	 */
	public function resolve($resource, $attribute = null)
	{
		$this->resource = $resource;
		$value = null;

		// It make no sense to resolve empty resource
		// It should be checked for null or empty array
		if (!empty($resource)) {
			$attribute = $attribute ?? $this->attribute;
			if (!$this->resolveCallback) {
				$value = $this->resolveAttribute($resource, $attribute);
			}
			elseif (is_callable($this->resolveCallback)) {
				$value = call_user_func(
					$this->resolveCallback, data_get($resource, $attribute), $resource, $this
				);
			}
		}

		// Provide empty array in case the field is multiple
		if (!$value && $this->multiple) {
			$value = [];
		}

		return $value;
	}

	/**
	 * Resolve the given attribute from the given resource.
	 *
	 * @param  mixed  $resource
	 * @param  string $attribute
	 *
	 * @return mixed
	 */
	protected function resolveAttribute($resource, $attribute)
	{
		return data_get($resource, $attribute);
	}

	/**
	 * @inheritdoc
	 */
	public function toArray()
	{
		$array = parent::toArray();

		// Native properties
		$array['component'] = $this->component;

		// Allow fields to modify to array result
		if (is_callable($this->arrayCallback)) {
			$array = call_user_func($this->arrayCallback, $array, $this);
		}

		return $array;
	}

	protected function getLabel()
	{
		return array_get(trans('attributes'), $this->attribute, studly_case($this->attribute));
	}

	/**
	 * Hydrate the given attribute on the model based on the incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  string                   $requestAttribute
	 * @param  object                   $model
	 * @param  string                   $attribute
	 *
	 * @return void
	 */
	protected function fillAttributeFromRequest(Request $request, $requestAttribute, $model, $attribute)
	{
		$value = $request[$requestAttribute];

		$value = $this->multiple ?
			array_map([$this, 'sanitizeRequestInput'], Arr::wrap($value)) :
			$this->sanitizeRequestInput($value);

		// If attribute path has dots, that means filling value into a deep array
		// For eloquent model or other objects with overloaded properties
		// we should take first level value and then fill deep value
		if (strpos($attribute, '.') !== false) {
			list($attribute, $path) = explode('.', $attribute, 2);
			$complexValue = data_get($model, $attribute) ?? [];
			data_set($complexValue, $path, $value);
			data_set($model, $attribute, $complexValue);
		}
		else {
			data_set($model, $attribute, $value);
		}

	}

	/**
	 * Sanitize value from request
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	protected function sanitizeRequestInput($value)
	{
		return $value;
	}

	/**
	 * Creates new instance of the field
	 *
	 * @param mixed ...$arguments
	 *
	 * @return static
	 */
	public static function make(...$arguments)
	{
		return new static(...$arguments);
	}

	/**
	 * @param callable $resolveCallback
	 *
	 * @return $this
	 */
	public function resolveCallback(callable $resolveCallback)
	{
		$this->resolveCallback = $resolveCallback;

		return $this;
	}

	/**
	 * @param callable $arrayCallback
	 *
	 * @return $this
	 */
	public function arrayCallback(callable $arrayCallback)
	{
		$this->arrayCallback = $arrayCallback;

		return $this;
	}

	/**
	 * Get currently resolving resource
	 *
	 * @return mixed
	 */
	public function getResource()
	{
		return $this->resource;
	}
}
