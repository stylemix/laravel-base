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

	protected function fillAttribute(Request $request, $requestAttribute, $model, $attribute)
	{
		if (! empty($request->{$requestAttribute})) {
			$model->{$attribute} = Hash::make($request[$requestAttribute]);
		}
	}

}
