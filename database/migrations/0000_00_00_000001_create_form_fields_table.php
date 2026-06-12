<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormFieldsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('form_fields', function (Blueprint $table) {
			$table->id();
			$table->timestamps();

			$table->string('label')->nullable();
			$table->string('field_type')->nullable();
			$table->integer('max_chars')->nullable();
			$table->foreignId('choice_group_id')->nullable();
			$table->string('allowed_files')->nullable();
			$table->integer('max_file_size')->nullable();
			$table->boolean('is_essential')->default(false);
			$table->integer('position')->nullable();
			$table->boolean('active')->default(true);

			$table->foreignId('form_generator_id')
				->nullable()
				->constrained()
				->cascadeOnDelete();
		});

		Schema::create('form_field_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('form_field_id');

			$table->string('title')->nullable();
			$table->string('explanations')->nullable();
			$table->string('main_logo_image')->nullable();

			$table->string('locale', 2)->index('ff_tr_l_i');
			$table->unique(['form_field_id', DB::raw('`locale`(2)')], 'ff_unique');
			$table->foreign('form_field_id', 'ff_foreign')->references('id')->on('form_fields')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('form_field_translations');
		Schema::dropIfExists('form_fields');
	}
}
