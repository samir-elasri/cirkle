<?php

namespace Database\Seeders;

use App\Imports\ExcelImport;
use App\Models\Core\Subscriber;
use App\Models\PostalCode;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SubscriberService;
use Illuminate\Database\Seeder;

/**
 * Fiche de démonstration MASTER 2350 :
 * importe la fiche ARBORISTE (0001 RF) puis crée un fournisseur DÉMO
 * avec un sous-ensemble de services cochés — la fiche publique montre
 * le rendu littéral (couleurs, espaces, sauts de bloc) sans attendre
 * les 200 fiches du client.
 *
 * Idempotent : ré-exécutable sans dupliquer.
 */
class DemoFicheSeeder extends Seeder
{
	public const DEMO_EMAIL = 'demo-arboriste@cirkleservices.com';

	public function run(): void
	{
		// 0. Délie d'abord le fournisseur démo : l'import purge les services non liés
		//    de la fiche — sinon les lignes encore cochées survivent et se dupliquent.
		Subscriber::where('email', self::DEMO_EMAIL)->first()
			?->subscriberServices()->delete();

		// 1. Importe (ou ré-importe) la fiche maître ARBORISTE
		$stats = (new ExcelImport())->import(
			database_path('seeders/data/master-2350-0001-rf-arboriste.xlsx')
		);
		$this->command?->info("Fiche importée : {$stats['profession']} ({$stats['services']} services, {$stats['capabilities']} capacités)");

		$profession = ServiceCategory::where('label', 'LIKE', '0001%')->firstOrFail();

		// 2. Fournisseur de démonstration, clairement marqué DÉMO
		$demo = Subscriber::firstOrNew(['email' => self::DEMO_EMAIL]);
		$demo->fill([
			'preference_language' => 'fr',
			'first_name' => 'Démo',
			'last_name' => 'Cirkle',
			'company_name' => 'DÉMO — Arboriste Exemple Inc.',
			'main_description' => 'Fiche de démonstration du formulaire de compétence MASTER 2350.',
			'is_provider' => true,
			'provider_type' => 'residential',
			'street' => '123 rue Exemple',
			'city' => 'Montréal',
			'postal_code' => 'H2X 1Y4',
			'phone' => '514 555-0100',
			'business_hours' => 'Lun-Ven 8h-17h',
			'active' => true,
			'is_public' => true,
			'registration_completed' => true,
			'accept_condition' => true,
			'email_validated' => true,
		]);
		$demo->service_category_id = $profession->id;

		// Province (Québec) : évite l'adresse avec virgule vide sur la fiche
		$demo->state_id = \App\Models\Core\State::all()
			->first(fn ($state) => str_starts_with(mb_strtolower($state->title ?? ''), 'q'))
			?->id;

		$demo->save();

		if (!PostalCode::where('subscriber_id', $demo->id)->count()) {
			PostalCode::create(['subscriber_id' => $demo->id, 'postal_code' => 'H2X 1Y4']);
		}

		// 3. Coche un sous-ensemble déterministe (~2 lignes sur 3) : les lignes non
		//    cochées disparaissent de la fiche publique, les sauts de bloc restent visibles.
		$demo->subscriberServices()->delete();

		$services = Service::where('service_category_id', $profession->id)
			->orderBy('source_row')
			->get();

		foreach ($services as $service) {
			if ($service->source_row % 3 === 0) {
				continue; // non coché : doit disparaître de la fiche publique
			}

			SubscriberService::create([
				'subscriber_id' => $demo->id,
				'service_id' => $service->id,
				'custom_value' => $service->has_input
					? 'Exemple fournisseur (démo)'
					: null,
			]);
		}

		// 4. Active deux options payantes (texte seulement, sans fichier) pour que la
		//    fiche démo montre des onglets payants : Permis et Estimation (feature #8).
		$demo->licenses()->delete();
		$license = new \App\Models\License(['subscriber_id' => $demo->id]);
		$license->translateOrNew('fr')->title = 'RBQ 1234-5678-90 (démo)';
		$license->translateOrNew('fr')->description = 'Licence d\'entrepreneur — exemple de démonstration.';
		$license->translateOrNew('en')->title = 'RBQ 1234-5678-90 (demo)';
		$license->translateOrNew('en')->description = 'Contractor licence — demonstration example.';
		$license->save();
		$demo->profile_license_active = true;

		$demo->profile_estimation_active = true;
		$demo->estimation_cost = 95;
		$demo->accepts_cash = true;
		$demo->accepts_debit = true;
		$demo->save();

		// Diplômes (option PDIPOMECK, feature #9) : deux exemples bilingues.
		$demo->profile_diploma_active = true;
		$demo->profile_diploma_activation_datetime = now();
		$demo->save();
		$demo->diplomas()->delete();
		$demoDiplomas = [
			['fr' => 'DEP Arboriculture', 'en' => 'Arboriculture diploma', 'school' => 'Centre de formation horticole de Laval', 'date' => '2015/06'],
			['fr' => 'Certificat — Abattage sécuritaire', 'en' => 'Certificate — Safe felling', 'school' => 'CNESST', 'date' => '2019/03'],
		];
		foreach ($demoDiplomas as $i => $d) {
			$diploma = new \App\Models\Diploma([
				'subscriber_id' => $demo->id,
				'school' => $d['school'],
				'graduated_at' => $d['date'],
				'position' => $i + 1,
			]);
			$diploma->translateOrNew('fr')->title = $d['fr'];
			$diploma->translateOrNew('en')->title = $d['en'];
			$diploma->save();
		}

		$this->command?->info(
			"Fournisseur démo #{$demo->id} ({$demo->formatted_member_number}) : "
			. $demo->subscriberServices()->count() . ' lignes cochées sur ' . $services->count()
			. ' ; onglets payants démo : Permis + Estimation'
		);
	}
}
