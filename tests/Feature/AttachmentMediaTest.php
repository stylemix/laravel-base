<?php

namespace Stylemix\Base\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Plank\Mediable\MediableServiceProvider;
use Plank\Mediable\UrlGenerators\LocalUrlGenerator;
use Stylemix\Base\Tests\DatabaseTestCase;
use Stylemix\Base\Tests\Dummy\DummyEntity;


class AttachmentMediaTest extends DatabaseTestCase
{
	protected const TEST_ATTACHMENT_URL = 'http://test.com/storage/document.pdf';

	protected function getPackageProviders($app)
	{
		return [
			MediableServiceProvider::class,
		];
	}

	protected function setUp(): void
	{
		parent::setUp();

		// UrlGenerator fails with fake file uploads when resolving public url.
		// Replacing UrlGenerator instance with partial mock
		$this->app->extend(LocalUrlGenerator::class, function ($urlBuilder) {
			$mock = \Mockery::mock($urlBuilder)->makePartial();
			$mock
				->shouldReceive('getUrl')
				->zeroOrMoreTimes()
				->andReturn(self::TEST_ATTACHMENT_URL);

			return $mock;
		});
	}

	public function testSaving()
	{
		Storage::fake('public');
		$file = UploadedFile::fake()->create('document.pdf');
		$model = new DummyEntity();
		$model->attachment_id = $file;
		$model->save();

		$this->assertTrue($model->hasMedia('attachment'));
		$value = $model->attachment;
		$this->assertEquals('public', $value->disk);
		$this->assertEquals('document.pdf', $value->filename);
		$this->assertEquals(self::TEST_ATTACHMENT_URL, $value->url);
	}
}
