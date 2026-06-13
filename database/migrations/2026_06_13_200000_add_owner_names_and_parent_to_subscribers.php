<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Feature #5 :
     * - owner_names : noms des propriétaires de l'entreprise (demandé par le client).
     * - parent_subscriber_id : lie le compte CLIENT auto-créé à son fournisseur
     *   (règle « auto-client-from-supplier », confirmée par Denis). Le client est une
     *   coquille sans login (email null) qui porte son propre numéro C — il sert au
     *   décompte des membres et aux tirages, sans entrer en collision avec l'email du
     *   fournisseur (voir docs/gap-map.md).
     */
    public function up(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->string('owner_names')->nullable()->after('company_name');
            $table->foreignId('parent_subscriber_id')->nullable()->after('is_provider')
                ->constrained('subscribers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscribers', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_subscriber_id');
            $table->dropColumn('owner_names');
        });
    }
};
