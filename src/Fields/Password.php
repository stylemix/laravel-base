<?php

namespace Stylemix\Base\Fields;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class Password extends Input
{
	public $type = 'password';

	public function getRules()
	{
		return array_merge(parent::getRules(), ['string']);
	}

	protected function fillAttributeFromRequest(Request $request, $requestAttribute, $model, $attribute)
	{
		if (trim($value = $request[$requestAttribute]) !== '') {
			$model->{$attribute} = Hash::make($value);
		}
	}

	public function resolve($resource, $attribute = null)
	{
		// Password field should not be resolved from models
		return;
	}

}
