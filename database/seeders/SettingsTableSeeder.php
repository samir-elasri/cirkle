<?php

namespace Database\Seeders;

use Arr;
use App\Models\Core\Setting;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

	public function run()
	{
		$faker = Faker::create('fr_CA');
		$year = Carbon::today()->year;

		Setting::create(gatherTranslatables([
			'pub_group_id' => 1,
			// Expéditeur des courriels : doit correspondre au domaine d'envoi pour passer
			// SPF/DMARC (un placeholder « example.com » fait rejeter/spammer les courriels).
			'sender_email' => 'noreply@cirkleservices.com',
			'sender_email_name' => 'CIRKLE',
			'fr' => [
				'copyright_notice' => "Copyright {$year} - Nom de la compagnie - Tous droits réservés",
				'validation_email_title' => 'Validation du courriel',
				'validation_email_content' => 'Cliquez sur le lien suivant pour confirmer votre adresse courriel :<br>{{url_activation}}<br>',
				'reset_email_title' => 'Récupération du mot de passe',
				'reset_email_content' => 'Cliquez sur le lien suivant pour réinitialiser votre mot de passe :<br>{{url_activation}}<br>',
			],
			'en' => [
				'copyright_notice' => "Copyright {$year} - Company name - All right reserved",
				'validation_email_title' => 'Validation du courriel',
				'validation_email_content' => 'Click on the following link to confirm your email address :<br>{{url_activation}}<br>',
				'reset_email_title' => 'Password recovery',
				'reset_email_content' => 'Click on the following link to reset your password :<br>{{url_activation}}<br>',
			]
		]));
	}
}
