<?php

namespace Stylemix\Base\Fields;

class Email extends Input
{
	public $type = 'email';

	public function getRules()
	{
		return array_merge(parent::getRules(), ['email']);
	}

}
