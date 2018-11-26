<?php

namespace Stylemix\Base\Fields;

class Input extends Base
{
	public $component = 'text-field';

	public $type = 'text';

	public function toArray()
	{
		return array_merge(parent::toArray(), [
			'type' => $this->type,
		]);
	}

}
