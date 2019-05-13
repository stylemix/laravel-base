<?php

namespace Stylemix\Base\ExtraFields;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Plank\Mediable\Media;
use Stylemix\Base\Fields\Base;

/**
 * @property-read mixed $attached
 * @property string $mediaTag
 * @method $this mediaTag($tag)
 */
class AttachmentField extends Base
{

	public $component = 'attachment-field';

	/**
	 * @inheritDoc
	 */
	protected function getValueFromRequest(Request $request, $requestAttribute)
	{
		if ($this->multiple) {
			$files = Arr::wrap($request->request->get($requestAttribute, []));
			$files += Arr::wrap($request->files->get($requestAttribute, []));
			return ksort($files);
		}

		return $request[$requestAttribute];
	}

	/**
	 * @inheritdoc
	 */
	protected function resolveAttribute($data, $attribute)
	{
		$resolved = parent::resolveAttribute($data, $attribute);

		if (!$this->resource) {
			return $resolved;
		}

		$attached = collect(Arr::wrap(data_get($this->resource, $this->mediaTag)));

		$this->attached = $this->multiple ? $attached->all() : $attached->first();

		return $resolved;
	}

	public function toArray()
	{
		if ($this->multiple && !is_array($this->attached)) {
			$this->attached = [];
		}

		if (!$this->multiple && !$this->attached) {
			$this->attached = null;
		}

		return parent::toArray();
	}

	protected function getMediaJson(Media $media)
	{
		return (object) [
			'id' => $media->id,
			'url' => $media->getUrl(),
			'disk' => $media->disk,
			'directory' => $media->directory,
			'filename' => $media->filename . '.' . $media->extension,
			'mime_type' => $media->mime_type,
			'aggregate_type' => $media->aggregate_type,
			'size' => $media->size,
		];
	}

}
