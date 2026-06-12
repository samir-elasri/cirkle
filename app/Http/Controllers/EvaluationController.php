<?php

namespace App\Http\Controllers;

use App;
use App\Mail\AdminMail;
use App\Models\ContactedProvider;
use App\Models\Core\Subscriber;
use App\Models\Evaluation;
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

		$data = $request->all();

		$validator = Validator::make(
			$data,
			[
				'provider_id'       => 'required',
				'client_id' => 'required',
				'global_grade' => 'required',
				'service_quality_grade' => 'required',
				'reliability_grade' => 'required',
				'communication_grade' => 'required',
				'hourly_rate_grade' => 'required',
			]
		);

		if ($validator->fails()) {
			return Redirect::back()
				->withInput()
				->withErrors($validator)
				->with('error', __('evaluation.submit-fail'));
		}

		$evaluation = Evaluation::create($data);
		$evaluation->insulting = $this->containsProfanity($data['comment'], App::getLocale());
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
}
