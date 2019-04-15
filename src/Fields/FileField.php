<?php

namespace Stylemix\Base\Fields;

class FileField extends Base
{

	public $component = 'file-field';

	public function getRules()
	{
		return array_merge(parent::getRules(), ['file']);
	}

}
