<?php

namespace Stylemix\Base\Fields;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordField extends TextField
{

	public $type = 'password';

	protected function fillAttributeFromRequest(Request $request, $requestAttribute, $model, $attribute)
	{
		if (trim($value = $request[$requestAttribute]) !== '') {
			$model->{$attribute} = Hash::make($value);
		}
	}

	public function resolve($resource, $attribute = null)
	{
		// Password field should not be resolved from models
		return null;
	}

}
