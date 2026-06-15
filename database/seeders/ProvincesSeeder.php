<?php

namespace Database\Seeders;

use App\Models\Core\Country;
use App\Models\Core\State;
use Illuminate\Database\Seeder;

/**
 * Les 10 provinces canadiennes comme States (cahier de charges : forfaits PAR PROVINCE).
 *
 * Le forfait par défaut reste « par code postal » (SubscriptionPrice.state_id = NULL);
 * un forfait peut aussi viser une province précise (state_id renseigné). Cette seeder
 * garantit que les 10 provinces existent pour que l'import du MASTER 2350 puisse y
 * rattacher les prix par province.
 *
 * Taxes (mapping autoritatif des niceNames du modèle State) :
 *   gst = TPS (fédéral) · tvp = TVP/PST · tvh = TVH/HST · pst = TVQ (Québec).
 * Taux standards canadiens 2026. On NE touche PAS aux taxes d'un Québec déjà configuré.
 *
 * Idempotent — réexécutable (firstOrNew par label, Québec apparié par titre).
 */
class ProvincesSeeder extends Seeder
{
    /**
     * label => [fr, en, gst, tvp, tvh, pst]
     */
    private const PROVINCES = [
        'ON' => ['Ontario',                  'Ontario',                       null, null, 13,    null],
        'QC' => ['Québec',                   'Quebec',                        5,    null, null,  9.975],
        'BC' => ['Colombie-Britannique',     'British Columbia',              5,    7,    null,  null],
        'AB' => ['Alberta',                  'Alberta',                       5,    null, null,  null],
        'MB' => ['Manitoba',                 'Manitoba',                      5,    7,    null,  null],
        'SK' => ['Saskatchewan',             'Saskatchewan',                  5,    6,    null,  null],
        'NS' => ['Nouvelle-Écosse',          'Nova Scotia',                   null, null, 14,    null],
        'NB' => ['Nouveau-Brunswick',        'New Brunswick',                 null, null, 15,    null],
        'NL' => ['Terre-Neuve-et-Labrador',  'Newfoundland and Labrador',     null, null, 15,    null],
        'PE' => ['Île-du-Prince-Édouard',    'Prince Edward Island',          null, null, 15,    null],
    ];

    public function run(): void
    {
        $countryId = State::query()->whereNotNull('country_id')->value('country_id')
            ?? Country::query()->value('id');

        $created = 0;
        $matched = 0;

        foreach (self::PROVINCES as $label => [$fr, $en, $gst, $tvp, $tvh, $pst]) {
            // Apparier un enregistrement existant : par label, sinon par titre (cas Québec déjà seedé).
            $state = State::where('label', $label)->first()
                ?? State::whereTranslation('title', $fr)->first()
                ?? State::whereTranslation('title', $en)->first();

            $isNew = $state === null;
            if ($isNew) {
                $state = new State();
            }

            $state->label = $label;
            if ($countryId) {
                $state->country_id = $countryId;
            }
            $state->active = true;

            // Ne pas écraser des taxes déjà saisies (Québec existant) : on ne renseigne
            // les taxes que pour les provinces nouvellement créées.
            if ($isNew) {
                $state->gst = $gst;
                $state->tvp = $tvp;
                $state->tvh = $tvh;
                $state->pst = $pst;
            }

            $state->save();

            if (empty($state->translateOrNew('fr')->title)) {
                $state->translateOrNew('fr')->title = $fr;
            }
            if (empty($state->translateOrNew('en')->title)) {
                $state->translateOrNew('en')->title = $en;
            }
            $state->save();

            $isNew ? $created++ : $matched++;
        }

        $this->command?->info("Provinces : {$created} créée(s), {$matched} appariée(s) — 10 provinces canadiennes prêtes.");
    }
}
