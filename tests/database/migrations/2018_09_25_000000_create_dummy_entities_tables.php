<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDummyEntitiesTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dummy_entities', function (Blueprint $table) {
			$table->increments('id');
			$table->string('text')->nullable();
			$table->string('text_exclude')->nullable();
			$table->string('enum', 16)->nullable();
			$table->string('slug')->nullable();
			$table->text('long_text')->nullable();
			$table->float('number')->unsigned()->nullable();
			$table->timestamp('datetime')->nullable();
			$table->unsignedInteger('attachment_id')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('dummy_entities');
	}
}
