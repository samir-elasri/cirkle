<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Remplace l'ancienne bannière du carrousel d'accueil (« …avant le 01 juin 2025…
 * aucun frais… ») par la promotion du crédit de 100 $ avant le 1er octobre 2026 (Denis).
 * Idempotent : cible le slide par son ANCIEN texte (pas par un id propre à la prod).
 */
class UpdatePromoSlide extends Command
{
    protected $signature = 'cirkle:update-promo-slide';
    protected $description = 'Remplace la bannière promo du carrousel par le crédit de 100 $ (1er oct. 2026)';

    public function handle(): int
    {
        $fr = 'BIENVENUE sur CIRKLE ! En vous inscrivant avant le 1er octobre 2026, vous recevrez un crédit de 100,00 $.';
        $en = 'WELCOME to CIRKLE! Register before October 1, 2026 and you will receive a $100.00 credit.';

        $frRows = DB::table('slide_translations')
            ->where('locale', 'fr')
            ->where(function ($q) {
                $q->where('content', 'like', '%AUCUN FRAIS%')
                  ->orWhere('content', 'like', '%juin 2025%');
            })
            ->update(['content' => $fr, 'title' => 'Promotion']);

        $enRows = DB::table('slide_translations')
            ->where('locale', 'en')
            ->where(function ($q) {
                $q->where('content', 'like', '%JUNE 1, 2025%')
                  ->orWhere('content', 'like', '%REGISTER BEFORE JUNE%')
                  ->orWhere('content', 'like', '%NO FEE%');
            })
            ->update(['content' => $en, 'title' => 'Promotion']);

        $this->info("Bannière promo mise à jour : {$frRows} FR, {$enRows} EN.");

        return self::SUCCESS;
    }
}
