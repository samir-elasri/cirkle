<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * « Précisez » multi-lignes (demandé par Denis) : le commentaire du fournisseur peut
 * s'étendre sur plusieurs lignes. custom_value passait de VARCHAR(191) à TEXT pour ne
 * pas tronquer. SQL brut (pas de doctrine/dbal requis). Forward-only.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE subscriber_services MODIFY custom_value TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE subscriber_services MODIFY custom_value VARCHAR(191) NULL');
    }
};
