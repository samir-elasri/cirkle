<?php

namespace Database\Seeders;

use App\Models\Core\Setting;
use Illuminate\Database\Seeder;

/**
 * Aligne les prix et libellés des 5 options payantes (feature #9) sur les fiches
 * du client (codes/prix verbatim) :
 *   PPERMITCK Permis 50 $ · PDIPOMECK Diplômes 50 $ · P12PICCK Photos 100 $ ·
 *   PESTCK2 Estimation 50 $ · PHIRECK Recrutement 100 $.
 * Idempotent — réexécutable.
 */
class AddonPricesSeeder extends Seeder
{
	public function run(): void
	{
		$setting = Setting::first();
		if (!$setting) {
			$this->command?->warn('Aucun réglage (settings) trouvé — prix non alignés.');
			return;
		}

		// Prix (colonnes plain sur settings)
		$setting->license_price = 50;     // Permis (PPERMITCK)
		$setting->diploma_price = 50;     // Diplômes (PDIPOMECK)
		$setting->image_price = 100;      // Photos (P12PICCK)
		$setting->estimation_price = 50;  // Estimation (PESTCK2)
		$setting->job_offer_price = 100;  // Recrutement (PHIRECK)
		$setting->diploma_order = 15;

		// Libellés de l'option Diplômes (traduits) — seulement s'ils sont vides
		foreach (['fr' => 'Diplômes académiques', 'en' => 'Academic diplomas'] as $locale => $title) {
			if (empty($setting->translateOrNew($locale)->diploma_title)) {
				$setting->translateOrNew($locale)->diploma_title = $title;
			}
		}
		foreach ([
			'fr' => 'Diplômes, certificats et formations de votre personnel (option payante).',
			'en' => 'Diplomas, certificates and training of your staff (paid option).',
		] as $locale => $desc) {
			if (empty($setting->translateOrNew($locale)->diploma_description)) {
				$setting->translateOrNew($locale)->diploma_description = $desc;
			}
		}

		$setting->save();

		$this->command?->info('Prix des options alignés : Permis 50$, Diplômes 50$, Photos 100$, Estimation 50$, Recrutement 100$.');
	}
}
