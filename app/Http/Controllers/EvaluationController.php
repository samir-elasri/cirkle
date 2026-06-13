<?php

namespace App\Http\Controllers;

use App;
use App\Mail\AdminMail;
use App\Models\ContactedProvider;
use App\Models\Core\Subscriber;
use App\Models\Evaluation;
use Auth;
use Illuminate\Http\Request;
use Mail;
use Redirect;
use URL;
use Validator;

class EvaluationController extends Controller
{
	use ProfanityApiTrait;

	public function create($params, Request $request, $client_id, $provider_id) {

		$client = Subscriber::findOrFail($client_id);
		$provider = Subscriber::findOrFail($provider_id);
		$contact = ContactedProvider::firstWhere([
			'client_id' => $client_id,
			'provider_id' => $provider_id,
		]);

		return array_merge($params, compact([
			'client',
			'provider',
			'contact',
		]));

	}

	public function store(Request $request) {

		// Évaluation façon Google : seuls les CLIENTS connectés peuvent noter (spec).
		$client = Auth::guard('subscribers')->user();
		if (!$client) {
			return Redirect::back()->with('error', __('evaluation.login-required'));
		}

		$data = $request->all();
		$data['client_id'] = $client->id; // jamais depuis le formulaire

		// On ne note pas sa propre fiche.
		if ((int) $data['provider_id'] === (int) $client->id) {
			return Redirect::back()->with('error', __('evaluation.no-self'));
		}

		$validator = Validator::make(
			$data,
			[
				'provider_id'  => 'required|exists:subscribers,id',
				'global_grade' => 'required|numeric|min:1|max:5',
				'comment'      => 'nullable|string|max:2000',
			]
		);

		if ($validator->fails()) {
			return Redirect::back()
				->withInput()
				->withErrors($validator)
				->with('error', __('evaluation.submit-fail'));
		}

		$evaluation = Evaluation::create($data);
		$evaluation->insulting = $this->containsProfanity($data['comment'] ?? '', App::getLocale());
		$evaluation->validated = !$evaluation->insulting;
		$evaluation->treated = $evaluation->insulting;
		$evaluation->save();

		if ($evaluation->has_less_than_two) {
			$mailTitle = setting('low_evaluation_title');
			$mailContent = str_replace(
				'{{url}}',
				URL::to('/admin/evaluations/' . $evaluation->id . '/edit'),
				setting('low_evaluation_text')
			);
			Mail::to(setting('reception_email'))
				->send(new AdminMail($mailContent, $mailTitle));
		}

		if ($evaluation->insulting) {
			$client = Subscriber::findOrNew($data['client_id']);

			$mailTitle = setting('insulting_evaluation_title');
			$mailContent = str_replace(
				[
					'{{client_id}}',
					'{{client_url}}',
					'{{evaluation_url}}',
				],
				[
					$client->id,
					URL::to('/admin/subscribers/' . $client->id . '/edit'),
					URL::to('/admin/evaluations/' . $evaluation->id . '/edit'),
				],
				setting('insulting_evaluation_content')
			);

			Mail::to(setting('reception_email'))
				->send(new AdminMail($mailContent, $mailTitle));
		}

		return Redirect::back()
			->with('success', __('evaluation.submit-success'));
	}

	/**
	 * Réponse du fournisseur à un avis (feature #10). En attente d'approbation admin
	 * (feature #14) avant publication. Seul le fournisseur visé peut répondre.
	 */
	public function reply(Request $request) {
		$provider = Auth::guard('subscribers')->user();
		if (!$provider) {
			return Redirect::back()->with('error', __('evaluation.login-required'));
		}

		$validator = Validator::make($request->all(), [
			'evaluation_id' => 'required|exists:evaluations,id',
			'reply'         => 'required|string|max:2000',
		]);

		if ($validator->fails()) {
			return Redirect::back()->withInput()->withErrors($validator);
		}

		$evaluation = Evaluation::findOrFail($request->input('evaluation_id'));

		// Le répondant doit être le fournisseur évalué.
		if ((int) $evaluation->provider_id !== (int) $provider->id) {
			return Redirect::back()->with('error', __('evaluation.reply-forbidden'));
		}

		$evaluation->reply = $request->input('reply');
		$evaluation->reply_approved = false; // doit être approuvée par un admin
		$evaluation->reply_created_at = now();
		$evaluation->save();

		// Avertit Cirkle qu'une réponse attend approbation.
		if (setting('reception_email')) {
			Mail::to(setting('reception_email'))->send(new AdminMail(
				URL::to('/admin/evaluations/' . $evaluation->id . '/edit'),
				__('evaluation.reply-pending-admin')
			));
		}

		return Redirect::back()->with('success', __('evaluation.reply-submitted'));
	}
}
