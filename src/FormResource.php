<?php

namespace Stylemix\Base;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Stylemix\Base\Fields\Base;

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
		parent::__construct($model);
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
	 * Fill data into resource model from a request
	 *
	 * @param object $resource
	 * @param \Illuminate\Http\Request|null $request
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function fill($resource, Request $request = null)
	{
		$this->fillRequestInto($request, $resource);

		return $resource;
	}

	/**
	 * Fill only specified fields
	 *
	 * @param mixed $resource
	 * @param array $fields List of field names
	 * @param \Illuminate\Http\Request|null $request
	 *
	 * @throws \Exception
	 */
	public function fillOnly($resource, array $fields, Request $request = null)
	{
		$this->fillRequestInto($request, $resource, function (Base $field) use ($fields) {
			return in_array($field->attribute, $fields);
		});
	}

	/**
	 * Fill only specified fields
	 *
	 * @param mixed $resource
	 * @param array $fields List of field names
	 * @param \Illuminate\Http\Request|null $request
	 *
	 * @throws \Exception
	 */
	public function fillExcept($resource, array $fields, Request $request = null)
	{
		$this->fillRequestInto($request, $resource, function (Base $field) use ($fields) {
			return !in_array($field->attribute, $fields);
		});
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 * @param $resource
	 * @param null $fieldsFilter
	 *
	 * @throws \Exception
	 */
	protected function fillRequestInto(Request $request, $resource, $fieldsFilter = null)
	{
		if (!is_object($resource)) {
			throw new \Exception('$resource should be an object to allow fields modify it without returning');
		}

		$request = $request ?: Container::getInstance()->make('request');
		$fields  = $this->getFields();

		if ($fieldsFilter) {
			$fields = $fields->filter($fieldsFilter);
		}

		$fields->each->fill($request, $resource);
	}

	public function toArray($request)
	{
		// resource resolved to array
		$data = parent::toArray($request);

		// replaced with field resolved data
		foreach ($this->getFields() as $field) {
			// Some fields are required to have access to original resource
			$field->setResource($this->resource);

			data_set($data, $field->attribute, $field->resolve($data));
		}

		if (empty($data)) {
			$data = new \stdClass();
		}

		return [
			'fields' => $this->getFields()->toArray(),
			'data' => $data,
		];
	}

	protected function isUpdate(Request $request)
	{
		return in_array($request->method(), ['PUT', "PATCH"]);
	}

}
