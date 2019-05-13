<?php

namespace Stylemix\Base\Attributes;

class AttachmentImage extends Attachment
{

	public function __construct(string $name)
	{
		parent::__construct($name);
		$this->mimeTypes = 'image/*';
	}

	/**
	 * @inheritdoc
	 */
	protected function uploadBuilder($item)
	{
		return parent::uploadBuilder($item)
			->setAllowedAggregateTypes(['image']);
	}

}
