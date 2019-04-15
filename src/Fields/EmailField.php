<?php

namespace Stylemix\Base\Fields;

class EmailField extends TextField
{
	public $type = 'email';

	public function getRules()
	{
		return array_merge(parent::getRules(), ['email']);
	}

}
