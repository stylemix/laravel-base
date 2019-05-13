<?php

namespace Stylemix\Base\Tests\Dummy;

use Stylemix\Base\Attributes\Attachment;
use Stylemix\Base\Attributes\AttachmentImage;
use Stylemix\Base\Attributes\Boolean;
use Stylemix\Base\Attributes\Datetime;
use Stylemix\Base\Attributes\Enum;
use Stylemix\Base\Attributes\Id;
use Stylemix\Base\Attributes\LongText;
use Stylemix\Base\Attributes\Number;
use Stylemix\Base\Attributes\Relation;
use Stylemix\Base\Attributes\Slug;
use Stylemix\Base\Attributes\Text;
use Stylemix\Base\Entity;

class DummyEntity extends Entity
{

	/**
	 * Attribute definitions
	 *
	 * @return array
	 */
	protected static function attributeDefinitions() : array
	{
		return [
			Text::make('text'),
			Text::make('text_exclude')->excludeSort()->excludeFilter(),
			Enum::make('enum'),
			Slug::make('slug'),
			LongText::make('long_text'),
			Number::make('number'),
			Id::make(),
			Boolean::make('boolean'),
			Datetime::make('datetime'),
			Attachment::make('attachment'),
			AttachmentImage::make('attachment_image'),
			Relation::make('relation'),
			Relation::make('relation_e')
				->excludeSort()
				->excludeFilter()
				->excludeIncluding(),
		];
	}
}
