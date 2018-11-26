<?php

namespace Stylemix\Base;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class FormResource extends JsonResource
{

	/**
	 * The "data" wrapper that should be applied.
	 *
	 * @var string
	 */
	public static $wrap = null;

	/**
	 * @var \Stylemix\Base\Fields\Base[]|\Illuminate\Support\Collection Collected list of fields
	 */
	private $fields;

	public function __construct($model = null)
	{
		parent::__construct($model ?? []);
	}

	/**
	 * List of field definitions defined by descendant class
	 *
	 * @return \Stylemix\Base\Fields\Base[]
	 */
	abstract public function fields();

	/**
	 * List of field definitions
	 *
	 * @return \Stylemix\Base\Fields\Base[]|\Illuminate\Support\Collection
	 */
	public function getFields()
	{
		if (!$this->fields) {
			$this->fields = collect($this->fields());

			if ($this->resource) {
				$this->fields->each->resolve($this->resource);
			}
		}

		return $this->fields;
	}

	/**
	 * Get rules for the fields
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return array
	 */
	public function rules(Request $request)
	{
		$fields = $this->getFields()->keyBy('attribute');

		$rules = collect();
		$fields->each(function ($field) use ($request, $rules) {
			$key = $field->attribute;
			$fieldRules = $field->getRules($request);

			if ($this->isUpdate($request) && in_array('required', $fieldRules)) {
				array_unshift($fieldRules, 'sometimes');
			}

			if ($field->multiple) {
				$typeRules = array_diff($fieldRules, ['required', 'sometimes']);

				if (count($fieldRules = array_diff($fieldRules, $typeRules))) {
					$rules[$key] = array_values($fieldRules);
				}

				$rules[$key . '.*'] = array_values($typeRules);
			}
			elseif (count($fieldRules)) {
				$rules[$key] = $fieldRules;
			}
		});

		return $rules->all();
	}

	/**
	 * Assign a resource for the form
	 *
	 * @param mixed $resource
	 *
	 * @return $this
	 */
	public function setResource($resource)
	{
		$this->resource = $resource;

		return $this;
	}

	/**
	 * Fill given request into resource model
	 *
	 * @param \Illuminate\Http\Request|null $request
	 *
	 * @return mixed
	 */
	public function fill(Request $request = null)
	{
		$request = $request ?: Container::getInstance()->make('request');
		$this->getFields()->each->fill($request, $this->resource);

		return $this->resource;
	}

	public function toArray($request)
	{
		return [
			'fields' => $this->getFields(),
			'data' => parent::toArray($request),
		];
	}

	protected function isUpdate(Request $request)
	{
		return in_array($request->method(), ['PUT', "PATCH"]);
	}

}
