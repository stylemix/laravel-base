<?php

namespace Stylemix\Base\Traits;

trait ResourceContexts
{

	protected $context = null;

	/**
	 * Get array for current contextual request
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return mixed If context is not defined then false is returned
	 */
	protected function toContextArray($request)
	{
		$context = $this->getContext($request) ?: 'default';

		if (method_exists($this, $method = $context . 'Context')) {
			return $this->$method($request);
		}

		return false;
	}

	/**
	 * Get current context
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return mixed
	 */
	protected function getContext($request)
	{
		if ($this->context !== null) {
			return $this->context;
		}

		return $request->get('context');
	}

	/**
	 * Check current context
	 *
	 * @param string $context
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return bool
	 */
	protected function isContext($context, $request)
	{
		return $this->getContext($request) == $context;
	}

	/**
	 * Set current context
	 *
	 * @param null $context
	 *
	 * @return $this
	 */
	public function setContext($context)
	{
		$this->context = $context;

		return $this;
	}
}
