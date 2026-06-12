<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocDocumentsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bloc_documents', function (Blueprint $table) {
			$table->id();

			$table->text('associate_documents')->nullable();

			$table->timestamps();
		});

		Schema::create('bloc_document_translations', function (Blueprint $table) {
			$table->id();
			$table->foreignId('bloc_document_id');

			$table->string('title')->nullable();

			$table->string('locale', 2)->index('bd_tr_l_i');
			$table->unique(['bloc_document_id', DB::raw('`locale`(2)')], 'bd_unique');
			$table->foreign('bloc_document_id', 'bd_id_foreign')->references('id')->on('bloc_documents')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bloc_document_translations');
		Schema::dropIfExists('bloc_documents');
	}
}
