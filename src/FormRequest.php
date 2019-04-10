<?php

namespace Stylemix\Base;

use Illuminate\Foundation\Http\FormRequest as IlluminateFormRequest;

abstract class FormRequest extends IlluminateFormRequest
{
	protected $formResource;

	/**
	 * @return \Stylemix\Base\FormResource
	 */
	abstract protected function formResource();

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return $this->getFormResource()->rules($this);
	}

	/**
	 * Fill the request into the given resource
	 *
	 * @param mixed $resource
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function fill($resource)
	{
		return $this->getFormResource()
			->fill($resource, $this);
	}

	/**
	 * Fill the request into the given resource only for specified fields
	 *
	 * @param mixed $resource
	 * @param array $fields
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function fillOnly($resource, array $fields)
	{
		return $this->getFormResource()
			->fillOnly($resource, $fields, $this);
	}

	/**
	 * Fill the request into the given resource except specified fields
	 *
	 * @param mixed $resource
	 * @param array $fields
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function fillExcept($resource, array $fields)
	{
		return $this->getFormResource()
			->fillExcept($resource, $fields, $this);
	}

	/**
	 * @return \Stylemix\Base\FormResource
	 */
	protected function getFormResource()
	{
		if (!$this->formResource) {
			$this->formResource = $this->formResource();
		}

		return $this->formResource;
	}

}
