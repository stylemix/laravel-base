<?php

namespace Stylemix\Base\Fields;

/**
 * @property $options Editor (quill) options
 * @method $this options($options) Set editor (quill) options
 * @property $modules Editor's (quill) modules
 * @method $this modules($modules) Set just editor (quill) modules
 */
class EditorField extends Base
{

	public $component = 'editor-field';

	protected $defaults = [
		'options' => null,
		'modules' => null,
	];
}
