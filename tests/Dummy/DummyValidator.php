<?php

namespace Stylemix\Base\Tests\Dummy;

use Illuminate\Contracts\Validation\Rule;

class DummyValidator implements Rule
{

	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string $attribute
	 * @param  mixed $value
	 *
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		return $value === 'dummy';
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string|array
	 */
	public function message()
	{
		return 'Dummy value is invalid';
	}
}
