<?php

namespace Stylemix\Base\Fields;

/**
 * @property $options Editor (quill) options
 * @method $this options($options) Set editor (quill) options
 * @property $modules Editor's (quill) modules
 * @method $this modules($modules) Set just editor (quill) modules
 */
class Editor extends Base
{

	public $component = 'editor-field';
}