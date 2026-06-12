<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('documents', function (Blueprint $table) {
			$table->id();

			$table->string('label')->nullable();
			$table->datetime('date')->nullable();
			$table->foreignId('doc_type')->nullable();

			$table->boolean('active')->default(0);
			$table->timestamps();
		});

		Schema::create('document_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('document_id');

			$table->string('title')->nullable();
			$table->string('filename')->nullable();
			$table->text('description')->nullable();
			$table->text('keywords')->nullable();
			$table->string('vignette_image')->nullable();
			$table->text('content')->nullable();

			$table->string('locale', 2)->index('bd_tr_l_i');
			$table->unique(['document_id', DB::raw('`locale`(2)')], 'dcmt_unique');
			$table->foreign('document_id', 'dcmt_id_foreign')->references('id')->on('documents')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('document_translations');
		Schema::dropIfExists('documents');
	}
}
