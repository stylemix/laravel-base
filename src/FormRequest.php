<?php

namespace Stylemix\Base;

use Illuminate\Foundation\Http\FormRequest as IlluminateFormRequest;

abstract class FormRequest extends IlluminateFormRequest
{
	protected $formResource;

	/**
	 * @param mixed $resource
	 * @return \Stylemix\Base\FormResource
	 */
	abstract protected function formResource($resource = null);

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return $this->formResource()->rules($this);
	}

	/**
	 * Fill the request to the given resource
	 *
	 * @param mixed $resource
	 *
	 * @return mixed
	 */
	public function fill($resource = null)
	{
		$resource = $resource ?? new \stdClass();

		return $this->getFormResource()
			->setResource($resource)
			->fill($this);
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