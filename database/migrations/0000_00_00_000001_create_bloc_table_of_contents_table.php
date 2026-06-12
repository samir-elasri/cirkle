<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlocTableOfContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bloc_table_of_contents', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

		Schema::create('bloc_table_of_content_translations', static function (Blueprint $table) {
			$table->id();

			$table->foreignId('bloc_table_of_content_id');

			$table->string('title')->nullable();

			$table->string('locale', 2)->index('btoc_tr_l_i');
			$table->unique(['bloc_table_of_content_id', DB::raw('`locale`(2)')], 'btoc_unique');

			$table->foreign('bloc_table_of_content_id', 'btoc_id_foreign')
				->references('id')
				->on('bloc_table_of_contents')
				->cascadeOnDelete();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
		Schema::dropIfExists('bloc_table_of_content_translations');
        Schema::dropIfExists('bloc_table_of_contents');
    }
}
