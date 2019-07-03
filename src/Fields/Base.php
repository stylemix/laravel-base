<?php

namespace Stylemix\Base\Fields;

use Closure;
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
 * @property string $helpText Help text for field
 * @method $this helpText($help) Set help text for field
 * @property boolean $required  Field value is required
 * @method $this required($value = true) Set as required
 * @property boolean $nullable  Field value can be null
 * @method $this nullable($nullable = true) Set flag that value can be nullable
 * @property boolean  $multiple  Multiple mode
 * @method $this multiple($value = true) Set as multiple
 * @property mixed $initialValue  Initial value for empty model
 * @method $this initialValue($initialValue) Set initial value for empty model
 */
abstract class Base extends Fluent
{

	/**
	 * @var array|string Additional rules
	 */
	public $rules;

	/**
	 * Rules of specific field type that should be merged to final rules list
	 * @var array
	 */
	protected $typeRules = [];

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
			'nullable' => true,
		];

		foreach ($defaults as $key => $value) {
			$this->offsetSet($key, $value);
		}

		$this->attribute = $attribute;
		$this->label     = Str::ucfirst(str_replace('_', ' ', Str::snake($attribute)));

		$this->trans('attributes');
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
		$rules = $this->getNormalizedRules();

		if ($this->required) {
			array_unshift($rules, 'required');
		}

		if ($this->nullable) {
			array_unshift($rules, 'nullable');
		}

		if ($this->multiple) {
			return array_filter(['array', '*' => $rules]);
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
	 * @param  mixed       $data
	 * @param  string|null $attribute
	 *
	 * @return mixed
	 */
	public function resolve($data, $attribute = null)
	{
		$value = null;

		// It make no sense to resolve empty resource
		// It should be checked for null or empty array
		if (!empty($data)) {
			$attribute = $attribute ?? $this->attribute;
			$value = $this->resolveAttribute($data, $attribute);

			if (is_callable($this->resolveCallback)) {
				$value = call_user_func(
					$this->resolveCallback, $value, $data, $this
				);
			}

			$value = $this->multiple ?
				array_map([$this, 'sanitizeResolvedValue'], Arr::wrap($value)) :
				// We shouldn't sanitize null value, since it means no value
				(is_null($value) ? null : $this->sanitizeResolvedValue($value));
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
	 * @param  mixed  $data
	 * @param  string $attribute
	 *
	 * @return mixed
	 */
	protected function resolveAttribute($data, $attribute)
	{
		return data_get($data, $attribute);
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

	/**
	 * Take translations from provided translation resource
	 *
	 * @param string $base
	 * @param string $attribute
	 *
	 * @return $this
	 */
	public function trans($base, $attribute = null)
	{
		$attribute = $attribute ?? $this->attribute;
		$trans = trans($base . '.' . $attribute);

		if (isset($trans['label'])) {
			$this->label = $trans['label'];
		}

		if (isset($trans['placeholder'])) {
			$this->placeholder = $trans['placeholder'];
		}

		if (isset($trans['help_text'])) {
			$this->helpText = $trans['help_text'];
		}

		return $this;
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
		$value = $this->getValueFromRequest($request, $requestAttribute);

		$value = $this->multiple ?
			array_map([$this, 'sanitizeRequestInput'], Arr::wrap($value)) :
			// We shouldn't sanitize null value, since it means no value
			(is_null($value) ? null : $this->sanitizeRequestInput($value));

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
	 * Method that can be overridden by some fields for retrieving input value.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param $requestAttribute
	 *
	 * @return mixed
	 */
	protected function getValueFromRequest(Request $request, $requestAttribute)
	{
		return $request[$requestAttribute];
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
	 * Field classes should override this method to sanitize resolved value to proper format
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	protected function sanitizeResolvedValue($value)
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
	 * Set the resource that have to be resolved
	 *
	 * @param mixed $resource
	 *
	 * @return Base
	 */
	public function setResource($resource)
	{
		$this->resource = $resource;

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
		return $value instanceof Closure ? $value($this, ...$arguments) : $value;
	}

	/**
	 * Converts client provided rules to array of rules
	 * and merges specific type rules
	 *
	 * @return array
	 */
	protected function getNormalizedRules() : array
	{
		$rules = Arr::wrap($this->rules);

		if (isset($rules[0]) && is_string($rules[0]) && Str::contains($rules[0], '|')) {
			$rules = explode('|', $rules[0]);
		}

		return array_merge($this->getTypeRules(), $rules);
	}

	/**
	 * Getter for type rules to allow implement custom behaviour
	 * @return array
	 */
	protected function getTypeRules() : array
	{
		return $this->typeRules;
	}
}
