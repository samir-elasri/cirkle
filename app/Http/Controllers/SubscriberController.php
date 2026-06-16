<?php

namespace App\Http\Controllers;

use App\Models\Core\Category;
use App\Models\Core\Purchase;
use App\Models\Core\Subscriber;
use App\Models\Core\Subscription;
use App\Models\Core\State;
use App\Models\Core\Country;
use App\Models\JobOffer;
use App\Models\License;
use App\Models\PostalCode;
use App\Models\SubscriptionPrice;
use App\Models\Promotion;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\SubscriberImage;
use App\Models\SubscriberService;
use Illuminate\Http\Request;
use Validator;
use Redirect;
use View;
use Arr;
use Cart;
use Auth;
use DB;
use Storage;
use Log;
use File;
use ModelUtility as ModelUtilityFacade;

class SubscriberController extends Controller
{
	public function createBasic(&$params, Request $request) {
		return $params;
	}

	public function createStep1(&$params, Request $request) {
		$subscriber = auth('subscribers')->user();
		if ($subscriber && $subscriber->registration_completed) {
			return Redirect::to(urlRouteName('home'))->with('error', __('auth.already-loggedin'));
		}

		$request->session()->forget([
			'registerFormData.step-2-validated',
			'registerFormData.step-3-validated',
			'registerFormData.step-4-validated',
			'registerFormData.step-5-validated'
		]);

		$params['states'] = State::where('active', '=', true)->get();
		$params['countries'] = Country::where('active', '=', true)->get();
		$params['legalForms'] = Category::getListByIdentifier('legal_forms');

		return $params;
	}

	public function createStep2(&$params, Request $request) {
		if (!$request->session()->get('registerFormData.step-1-validated')) {
			return Redirect::to(urlRouteName('register-supplier-step-1'))
				->with('error', __('auth.step-unvalidated'));
		}

		$request->session()->forget([
			'registerFormData.step-3-validated',
			'registerFormData.step-4-validated',
			'registerFormData.step-5-validated'
		]);

		$params['subcategories'] = ServiceCategory::where('active', '=', true)
			->whereNotNull('service_category_id')
			->get()
			->sortBy('title')
			->filter(function ($model) { return !empty($model->title); });

		$params['services'] = Service::where('active', '=', true)
			->whereNotNull('service_category_id')
			->get()
			->sortBy('title')
			->filter(function ($model) { return !empty($model->title); });

		return $params;
	}

	public function createStep3(&$params, Request $request) {
		if (!$request->session()->get('registerFormData.step-2-validated')) {
			return Redirect::to(urlRouteName('register-supplier-step-2'))
				->with('error', __('auth.step-unvalidated'));
		}

		$request->session()->forget([
			'registerFormData.step-4-validated',
			'registerFormData.step-5-validated'
		]);

		return $params;
	}

	public function createStep4(&$params, Request $request) {
		if (!$request->session()->get('registerFormData.step-3-validated')) {
			return Redirect::to(urlRouteName('register-supplier-step-3'))
				->with('error', __('auth.step-unvalidated'));
		}
	
		$request->session()->forget([
			'registerFormData.step-5-validated'
		]);

		$selectedCategory = $request->session()->get('registerFormData.service_category_id');

		// Prix affiché par défaut = forfait CODE POSTAL (state_id NULL) de la catégorie.
		$params['subscriptions'] = Subscription::where('active', '=', true)
			->with(['subscriptionPrices' => function($query) use ($selectedCategory) {
				$query->where('subscription_prices.service_category_id', '=', $selectedCategory)
					->whereNull('subscription_prices.state_id');
			}])
			->orderBy('position')
			->get();

		// Forfaits par PROVINCE (cahier de charges) : carte (abonnement => zone => coût)
		// pour que la page calcule le prix selon la zone choisie (code postal ou province).
		$allPrices = SubscriptionPrice::where('service_category_id', '=', $selectedCategory)->get();
		$priceMap = [];
		foreach ($allPrices as $price) {
			$zone = $price->state_id === null ? 'postal' : (string) $price->state_id;
			$priceMap[$price->subscription_id][$zone] = (float) $price->cost;
		}
		$params['priceMap'] = $priceMap;

		// Provinces réellement tarifées pour cette catégorie (sinon : que le code postal).
		$provinceIds = $allPrices->whereNotNull('state_id')->pluck('state_id')->unique()->values()->all();
		$params['provinces'] = $provinceIds
			? State::whereIn('states.id', $provinceIds)->orderByTranslation('title')->get()
			: collect();

		return $params;
	}

	public function createStep5(&$params, Request $request) {
		if (!$request->session()->get('registerFormData.step-4-validated')) {
			return Redirect::to(urlRouteName('register-supplier-step-4'))
				->with('error', __('auth.step-unvalidated'));
		}

		$params['subscriber'] = $request->session()->get('subscriber_model');

		$params['profileOptions'] = [
			'license',
			'diploma',
			'promotion',
			'image',
			'estimation',
			'job_offer',
			'url',
		];

		return $params;
	}

	public function createStep6(&$params, Request $request) {
		if (!$request->session()->get('registerFormData.step-5-validated')) {
			return Redirect::to(urlRouteName('register-supplier-step-5'))
				->with('error', __('auth.step-unvalidated'));
		}

		return $params;
	}

	public function step2ServiceForm(Request $request) {
		$serviceCategory = ServiceCategory::findOrFail($request->input('service_category_id'));

		// Check if we're in edit mode by looking for subscriber data in request
		$isEdit = $request->has('subscriber_id') || $request->has('is_edit');

		// Porte d'acceptation des frais (feature #6) : à l'inscription, avant que le
		// formulaire ne se rende. Refus / pas encore accepté → seule la porte s'affiche.
		// En édition (membre déjà payant), on ne re-bloque pas.
		if (!$isEdit && !$this->ficheFeeAccepted($request, $serviceCategory->id)) {
			return View::make('partials.fee-gate', compact('serviceCategory'));
		}

		$existingData = [];

		if ($isEdit && auth('subscribers')->check()) {
			$subscriber = auth('subscribers')->user();
			$existingData = $this->extractSubscriberServiceData($subscriber);
		}

		return View::make('partials.service-form', array_merge(
			compact('serviceCategory'),
			$existingData
		));
	}

	/**
	 * Enregistre l'acceptation des frais de la fiche pour une profession (porte feature #6).
	 * Appelé en AJAX par le bouton « J'accepte » de la porte.
	 */
	public function acceptFee(Request $request) {
		$categoryId = (int) $request->input('service_category_id');

		if (!$categoryId || !ServiceCategory::whereKey($categoryId)->exists()) {
			return response()->json(['accepted' => false], 422);
		}

		$request->session()->put("fee_accepted.{$categoryId}", true);

		return response()->json(['accepted' => true]);
	}

	/**
	 * Les frais de la fiche ont-ils été acceptés pour cette profession ?
	 */
	private function ficheFeeAccepted(Request $request, $categoryId): bool
	{
		return (bool) $request->session()->get("fee_accepted.{$categoryId}");
	}

	/**
	 * Crée le compte CLIENT jumeau d'un fournisseur (règle auto-client, feature #5).
	 * Coquille sans login : email null (aucune collision avec l'email du fournisseur,
	 * la colonne email n'ayant pas d'index unique en base). Le hook boot du modèle lui
	 * attribue le prochain numéro de la séquence partagée → un numéro C. Idempotent.
	 */
	private function createPairedClient(Subscriber $supplier): void
	{
		if (Subscriber::where('parent_subscriber_id', $supplier->id)->exists()) {
			return;
		}

		$client = new Subscriber();
		$client->fill([
			'preference_language'     => $supplier->preference_language,
			'first_name'              => $supplier->owner_names ?: $supplier->company_name,
			'last_name'               => '',
			'is_provider'             => false,
			'is_public'               => false,
			'active'                  => true,
			'registration_completed'  => true,
			'accept_condition'        => true,
			'parent_subscriber_id'    => $supplier->id,
			'street'                  => $supplier->street,
			'city'                    => $supplier->city,
			'postal_code'             => $supplier->postal_code,
		]);
		$client->email = null; // coquille sans connexion
		$client->save();
	}

	/**
	 * Show step 1 editing form
	 */
	public function editStep1($params) {
		$subscriber = auth('subscribers')->user();

		$params['states'] = State::where('active', '=', true)->get();
		$params['countries'] = Country::where('active', '=', true)->get();
		$params['legalForms'] = Category::getListByIdentifier('legal_forms');

		$params['subscriber'] = $subscriber;
		$params['isEdit'] = true;

		return $params;
	}

	/**
	 * Show step 2 editing form
	 */
	public function editStep2($params) {
		$subscriber = auth('subscribers')->user();

		$params['subcategories'] = ServiceCategory::where('active', '=', true)
			->whereNotNull('service_category_id')
			->get()
			->sortBy('title')
			->filter(function ($model) { return !empty($model->title); });

		$params['services'] = Service::where('active', '=', true)
			->whereNotNull('service_category_id')
			->get()
			->sortBy('title')
			->filter(function ($model) { return !empty($model->title); });

		
		// Add subscriber data and edit flag
		$params['subscriber'] = $subscriber;
		$params['isEdit'] = true;
		
		// Extract existing service data
		$existingData = $this->extractSubscriberServiceData($subscriber);
		$params = array_merge($params, $existingData);

		return $params;
	}

	/**
	 * Update step 1 profile data
	 */
	public function updateStep1(Request $request) {
		$subscriber = auth('subscribers')->user();

		$data = $request->all([
			'preference_language',
			'company_name',
			'owner_names',
			'legal_form_id',
			'federal_tax_number',
			'street',
			'city',
			'postal_code',
			'phone',
			'toll_free_phone',
			'fax',
			'email',
			'start_date',
			'insurance_coverage',
			'business_hours',
		]);

		// Modify validation rules for editing - exclude current user from email uniqueness
		$validator = Validator::make($data, [
			'email' => 'required|email|unique:subscribers,email,' . $subscriber->id,
			'company_name' => 'required',
			'legal_form_id' => 'required',
			'federal_tax_number' => 'required',
			'street' => 'required',
			'city' => 'required',
			'postal_code' => 'required',
			'phone' => 'required',
			'start_date' => 'required',
			'business_hours' => 'required',
		]);

		$validator->setAttributeNames([
			'preference_language' => __('auth.register.preference_language'),
			'company_name' => __('auth.register.company_name'),
			'legal_form_id' => __('auth.register.legal_form_id'),
			'federal_tax_number' => __('auth.register.federal_tax_number'),
			'street' => __('auth.register.street'),
			'city' => __('auth.register.city'),
			'postal_code' => __('auth.register.postal_code'),
			'phone' => __('auth.register.phone'),
			'toll_free_phone' => __('auth.register.toll_free_phone'),
			'fax' => __('auth.register.fax'),
			'email' => __('auth.register.email'),
			'start_date' => __('auth.register.start_date'),
			'insurance_coverage' => __('auth.register.insurance_coverage'),
			'business_hours' => __('auth.register.business_hours'),
		]);
		
		if ($validator->fails()) {
			return redirect()->back()
				->withInput()
				->withErrors($validator);
		}

		// Update subscriber directly
		$subscriber->update($data);

		return Redirect::to(urlRouteName('profile'))->with('success', __('profile.step1.updated'));
	}

	/**
	 * Update step 2 profile data
	 */
	public function updateStep2(Request $request) {
		$subscriber = auth('subscribers')->user();

		$data = $request->all([
			'provider_type',
			'service_category_id',
			'services',
			'service_input',
			'custom_services',
			'capabilities',
			'capability_input',
			'custom_capabilities'
		]);

		// Filter service_input to only include selected services
		if (!empty($data['service_input']) && !empty($data['services'])) {
			$selectedServices = array_map('intval', $data['services']);
			$data['service_input'] = array_intersect_key(
				$data['service_input'], 
				array_flip($selectedServices)
			);
		} else {
			$data['service_input'] = [];
		}

		// Filter capability_input to only include selected capabilities  
		if (!empty($data['capability_input']) && !empty($data['capabilities'])) {
			$selectedCapabilities = array_map('intval', $data['capabilities']);
			$data['capability_input'] = array_intersect_key(
				$data['capability_input'], 
				array_flip($selectedCapabilities)
			);
		} else {
			$data['capability_input'] = [];
		}

		// Same validation rules as registration
		$validator = Validator::make($data, [
			'services' => 'required|array',
			'custom_services' => 'nullable|array',
			'service_input' => 'nullable|array',
			'capabilities' => 'required|array',
			'custom_capabilities' => 'nullable|array',
			'capability_input' => 'nullable|array',
		]);

		$validator->setAttributeNames([
			'services' => __('auth.register.services'),
			'service_input' => __('auth.register.service_input'),
			'custom_services' => __('auth.register.custom_services'),
			'capabilities' => __('auth.register.capabilities'),
			'capability_input' => __('auth.register.capability_input'),
			'custom_capabilities' => __('auth.register.custom_capabilities'),
		]);
		
		if ($validator->fails()) {
			return redirect()->back()
				->withInput()
				->withErrors($validator);
		}

		DB::transaction(function () use ($subscriber, $data) {

			// Delete existing subscriber services to rebuild them
			$subscriber->subscriberServices()->delete();

			// Rebuild subscriber services collection (reuse logic from storeStep3)
			$subscriberServices = collect();
			
			// Build services collection
			foreach ($data['services'] ?? [] as $serviceId) {
				$subscriberServices->push(new SubscriberService([
					'subscriber_id' => $subscriber->id,
					'service_id' => $serviceId
				]));
			}
			
			// Build custom services collection
			foreach ($data['custom_services'] ?? [] as $customServiceTitle) {
				$service = Service::create([
					'title' => $customServiceTitle,
					'type' => 'service',
				]);
				
				$subscriberServices->push(new SubscriberService([
					'subscriber_id' => $subscriber->id,
					'service_id' => $service->id,
				]));
			}
			
			// Build service input collection
			foreach ($data['service_input'] ?? [] as $serviceId => $customValue) {
				$existingService = $subscriberServices->first(function($ss) use ($serviceId) {
					return $ss->service_id == $serviceId;
				});
				
				if ($existingService) {
					$existingService->custom_value = $customValue;
				} else {
					$subscriberServices->push(new SubscriberService([
						'subscriber_id' => $subscriber->id,
						'service_id' => $serviceId,
						'custom_value' => $customValue
					]));
				}
			}
			
			// Build capabilities collection
			foreach ($data['capabilities'] ?? [] as $capabilityId) {
				$subscriberServices->push(new SubscriberService([
					'subscriber_id' => $subscriber->id,
					'service_id' => $capabilityId
				]));
			}
			
			// Build custom capabilities collection
			foreach ($data['custom_capabilities'] ?? [] as $customCapabilityTitle) {
				$service = Service::create([
					'title' => $customCapabilityTitle,
					'type' => 'capability',
				]);
				
				$subscriberServices->push(new SubscriberService([
					'subscriber_id' => $subscriber->id,
					'service_id' => $service->id,
				]));
			}
			
			// Build capability input collection
			foreach ($data['capability_input'] ?? [] as $capabilityId => $customValue) {
				$existingService = $subscriberServices->first(function($ss) use ($capabilityId) {
					return $ss->service_id == $capabilityId;
				});
				
				if ($existingService) {
					$existingService->custom_value = $customValue;
				} else {
					$subscriberServices->push(new SubscriberService([
						'subscriber_id' => $subscriber->id,
						'service_id' => $capabilityId,
						'custom_value' => $customValue
					]));
				}
			}
			
			// Save all subscriber services
			foreach ($subscriberServices as $subscriberService) {
				$subscriberService->save();
			}
		});

		return Redirect::to(urlRouteName('profile'))->with('success', __('profile.step2.updated'));
	}

	/**
	 * Display the edit form for profile options (Step 5)
	 * Only shows currently active profile options
	 */
	public function editStep5($params)
	{
		$subscriber = auth('subscribers')->user();
		
		if (!$subscriber) {
			return Redirect::to(urlRouteName('home'))
				->with('error', __('auth.must-be-logged-in'));
		}

		// Get list of active profile options
		$activeOptions = [];
		$allOptions = ['license', 'diploma', 'promotion', 'image', 'estimation', 'job_offer', 'url'];
		
		foreach ($allOptions as $option) {
			$activeField = "profile_{$option}_active";
			// Special case for URL which doesn't have an active field
			if ($option === 'url') {
				if ($subscriber->profile_url_activation_datetime) {
					$activeOptions[] = $option;
				}
			} elseif ($subscriber->$activeField) {
				$activeOptions[] = $option;
			}
		}

		// If no active options, redirect back with message
		if (empty($activeOptions)) {
			return Redirect::to(urlRouteName('profile'))
				->with('info', __('profile.no_active_options'));
		}

		// Load existing data for active options
		$subscriber->load(['licenses', 'promotions', 'subscriberImages', 'jobOffers']);

		$params['subscriber'] = $subscriber;
		$params['profileOptions'] = $activeOptions;
		$params['isEdit'] = true;

		return $params;
	}

	/**
	 * Process the profile options update (Step 5)
	 * Updates only currently active profile options
	 */
	public function updateStep5(Request $request)
	{
		$subscriber = auth('subscribers')->user();
		
		if (!$subscriber) {
			return Redirect::to(urlRouteName('home'))
				->with('error', __('auth.must-be-logged-in'));
		}

		// Get list of active profile options
		$activeOptions = [];
		$allOptions = ['license', 'diploma', 'promotion', 'image', 'estimation', 'job_offer', 'url'];
		
		foreach ($allOptions as $option) {
			$activeField = "profile_{$option}_active";
			if ($option === 'url') {
				if ($subscriber->profile_url_activation_datetime) {
					$activeOptions[] = $option;
				}
			} elseif ($subscriber->$activeField) {
				$activeOptions[] = $option;
			}
		}

		// Collect data only for active options
		$dataFields = [];
		foreach ($activeOptions as $option) {
			$dataFields[] = $option;
		}
		
		// Add specific fields for certain options
		if (in_array('estimation', $activeOptions)) {
			$dataFields = array_merge($dataFields, [
				'estimation_cost',
				'accepts_cash',
				'accepts_check',
				'accepts_debit',
				'accepts_credit'
			]);
		}
		
		if (in_array('url', $activeOptions)) {
			$dataFields = array_merge($dataFields, ['fr.url', 'en.url']);
		}

		$data = $request->only($dataFields);

		// Process file uploads if any
		$data = $this->processFileUploadsForSession($data, $request);

		// Validation rules only for active options
		$rules = [];
		foreach ($activeOptions as $option) {
			if ($option === 'estimation') {
				$rules['estimation_cost'] = 'nullable|numeric|min:0';
			} elseif ($option === 'url') {
				$rules['fr.url'] = 'nullable|url';
				$rules['en.url'] = 'nullable|url';
			}
		}

		$this->validate($request, $rules);

		DB::beginTransaction();
		try {
			// Update simple options on subscriber model
			if (in_array('estimation', $activeOptions)) {
				$subscriber->estimation_cost = $data['estimation_cost'] ?? null;
				$subscriber->accepts_cash = !empty($data['accepts_cash']);
				$subscriber->accepts_check = !empty($data['accepts_check']);
				$subscriber->accepts_debit = !empty($data['accepts_debit']);
				$subscriber->accepts_credit = !empty($data['accepts_credit']);
			}

			// Update URL translations
			if (in_array('url', $activeOptions)) {
				foreach (['fr', 'en'] as $locale) {
					if (isset($data[$locale]['url'])) {
						$subscriber->translateOrNew($locale)->url = $data[$locale]['url'];
					}
				}
			}

			$subscriber->save();

			// Handle complex options (delete and rebuild)
			// Get data from session for profile options
			$sessionData = $request->session()->all();

			// Licenses
			if (in_array('license', $activeOptions) && isset($sessionData['profile_licenses'])) {
				$subscriber->licenses()->delete();
				foreach ($sessionData['profile_licenses'] as $licenseData) {
					$license = new \App\Models\License($licenseData);
					$license->subscriber_id = $subscriber->id;
					$license->save();
				}
			}

			// Diplomas
			if (in_array('diploma', $activeOptions) && isset($sessionData['profile_diplomas'])) {
				$subscriber->diplomas()->delete();
				foreach ($sessionData['profile_diplomas'] as $diplomaData) {
					$diploma = new \App\Models\Diploma($diplomaData);
					$diploma->subscriber_id = $subscriber->id;
					$diploma->save();
				}
			}

			// Promotions
			if (in_array('promotion', $activeOptions) && isset($sessionData['profile_promotions'])) {
				$subscriber->promotions()->delete();
				foreach ($sessionData['profile_promotions'] as $promotionData) {
					$promotion = new \App\Models\Promotion($promotionData);
					$promotion->subscriber_id = $subscriber->id;
					$promotion->save();
				}
			}

			// Images
			if (in_array('image', $activeOptions) && isset($sessionData['profile_subscriber_images'])) {
				$subscriber->subscriberImages()->delete();
				foreach ($sessionData['profile_subscriber_images'] as $imageData) {
					// Move temp files to permanent location if needed
					if (isset($imageData['image']) && strpos($imageData['image'], 'temp/') !== false) {
						$imageData['image'] = $this->moveTemporaryFilesToFinalLocation(
							$imageData['image'], 
							$subscriber->id
						);
					}
					$image = new \App\Models\SubscriberImage($imageData);
					$image->subscriber_id = $subscriber->id;
					$image->save();
				}
			}

			// Job Offers
			if (in_array('job_offer', $activeOptions) && isset($sessionData['profile_job_offers'])) {
				$subscriber->jobOffers()->delete();
				foreach ($sessionData['profile_job_offers'] as $jobData) {
					$jobOffer = new \App\Models\JobOffer($jobData);
					$jobOffer->subscriber_id = $subscriber->id;
					$jobOffer->save();
				}
			}

			DB::commit();

			// Clear any temporary session data for profile options
			$request->session()->forget([
				'profile_licenses',
				'profile_diplomas',
				'profile_promotions',
				'profile_subscriber_images',
				'profile_job_offers'
			]);

			return Redirect::to(urlRouteName('profile'))
				->with('success', __('profile.options.updated'));

		} catch (\Exception $e) {
			DB::rollBack();
			\Log::error('Profile options update failed: ' . $e->getMessage());
			return redirect()->back()
				->withInput()
				->with('error', __('main.errorOccurred'));
		}
	}

	public function storeBasic(Request $request)
	{
		if (logged_in()) {
			return Redirect::to('/');
		}
		
		$data = $request->all([
			'preference_language',
			'first_name',
			'last_name',
			'email',
			'street',
			'city',
			'postal_code',
			'password',
			'password_confirmation',
			'accept_condition'
		]);

		$validator = Validator::make(
			$data,
			[
				'email' => 'required|email|unique:subscribers,email',
				'first_name' => 'required',
				'last_name' => 'required',
				'password'              => 'required|regex:/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/',
				'password_confirmation' => 'required_with:password|same:password',
				'accept_condition'      => 'accepted',
			]
		);

		$validator->setAttributeNames([
			'preference_language' => __('auth.register.preference_language'),
			'first_name' => __('auth.register.first_name'),
			'last_name' => __('auth.register.last_name'),
			'email' => __('auth.register.email'),
			'street' => __('auth.register.street'),
			'city' => __('auth.register.city'),
			'postal_code' => __('auth.register.postal_code'),
			'password' => __('auth.register.password'),
			'password_confirmation' => __('auth.register.password_confirmation'),
			'accept_condition' => __('auth.register.terms'),
		]);
		
		if ($validator->fails()) {
			return redirect()->back()
				->withInput()
				->withErrors($validator);
		}

		$data['active'] = true;

		$subscriber = new Subscriber();

		if ($subscriber->saveElement($data)) {
			Auth::guard('subscribers')->loginUsingId($subscriber->id);

			$token = $subscriber->recoveringToken();

			$data['url'] = urlRouteName('subscriber.validate', ['token' => $token], true);

			$numberMsg = __('main.memberNumber') . ' : ' . $subscriber->formatted_member_number;

			if ($subscriber->sendMail('register', $data)) {
				return Redirect::to(urlRouteName('profile'))
					->with('success', __('form.register_success_message') . ' — ' . $numberMsg);
			}
			else {
				// Compte créé (numéro attribué) même si le courriel n'est pas envoyé.
				return Redirect::to(urlRouteName('profile'))
					->with('success', __('form.register_success_message') . ' — ' . $numberMsg);
			}
		}

		return Redirect::to(urlRouteName('profile'))
			->with('error', __('main.errorOccurred'));
	}

	public function storeStep1(Request $request)
	{
		$data = $request->all([
			'preference_language',
			'company_name',
			'owner_names',
			'legal_form_id',
			'federal_tax_number',
			'street',
			'city',
			'postal_code',
			'phone',
			'toll_free_phone',
			'fax',
			'email',
			'start_date',
			'insurance_coverage',
			'business_hours',
		]);

		$validator = Validator::make(
			$data,
			[
				'email' => 'required|email|unique:subscribers,email',
				'company_name' => 'required',
				'legal_form_id' => 'required',
				'federal_tax_number' => 'required',
				'street' => 'required',
				'city' => 'required',
				'postal_code' => 'required',
				'phone' => 'required',
				'email' => 'required',
				'start_date' => 'required',
				'business_hours' => 'required',
			]
		);

		$validator->setAttributeNames([
			'preference_language' => __('auth.register.preference_language'),
			'company_name' => __('auth.register.company_name'),
			'legal_form_id' => __('auth.register.legal_form_id'),
			'federal_tax_number' => __('auth.register.federal_tax_number'),
			'street' => __('auth.register.street'),
			'city' => __('auth.register.city'),
			'postal_code' => __('auth.register.postal_code'),
			'phone' => __('auth.register.phone'),
			'toll_free_phone' => __('auth.register.toll_free_phone'),
			'fax' => __('auth.register.fax'),
			'email' => __('auth.register.email'),
			'start_date' => __('auth.register.start_date'),
			'insurance_coverage' => __('auth.register.insurance_coverage'),
			'business_hours' => __('auth.register.business_hours'),
		]);
		
		if ($validator->fails()) {
			return redirect()->back()
				->withInput()
				->withErrors($validator);
		}

		$data['step-1-validated'] = true;

		$request->session()->put('registerFormData', $data);

		return Redirect::to(urlRouteName('register-supplier-step-2'));
	}

	public function storeStep2(Request $request)
	{
		if (!$request->session()->has('registerFormData')) {
			return redirect()->back()->with('error', __('main.errorOccurred'));
		}

		$data = $request->all([
			'provider_type',
			'service_category_id',
			'services',
			'service_input',
			'custom_services',
			'capabilities',
			'capability_input',
			'custom_capabilities'
		]);

		// Filter service_input to only include selected services
		if (!empty($data['service_input']) && !empty($data['services'])) {
			$selectedServices = array_map('intval', $data['services']);
			$data['service_input'] = array_intersect_key(
				$data['service_input'], 
				array_flip($selectedServices)
			);
		} else {
			$data['service_input'] = [];
		}

		// Filter capability_input to only include selected capabilities  
		if (!empty($data['capability_input']) && !empty($data['capabilities'])) {
			$selectedCapabilities = array_map('intval', $data['capabilities']);
			$data['capability_input'] = array_intersect_key(
				$data['capability_input'], 
				array_flip($selectedCapabilities)
			);
		} else {
			$data['capability_input'] = [];
		}

		$validator = Validator::make(
			$data,
			[
				'provider_type' => 'required',
				'service_category_id' => 'required',
				'services' => 'required|array',
				'custom_services' => 'nullable|array',
				'service_input' => 'nullable|array',
				'capabilities' => 'required|array',
				'custom_capabilities' => 'nullable|array',
				'capability_input' => 'nullable|array',
			]
		);

		$validator->setAttributeNames([
			'provider_type' => __('auth.register.provider_type'),
			'service_category_id' => __('auth.register.service_category_id'),
			'services' => __('auth.register.services'),
			'service_input' => __('auth.register.service_input'),
			'custom_services' => __('auth.register.custom_services'),
			'capabilities' => __('auth.register.capabilities'),
			'capability_input' => __('auth.register.capability_input'),
			'custom_capabilities' => __('auth.register.custom_capabilities'),
		]);
		
		if ($validator->fails()) {
			return redirect()->back()
				->withInput()
				->withErrors($validator);
		}

		// Porte d'acceptation des frais (feature #6) : garde côté serveur.
		if (!$this->ficheFeeAccepted($request, $data['service_category_id'])) {
			return redirect()->back()
				->withInput()
				->with('error', __('auth.register.fee_not_accepted'));
		}

		$data['step-2-validated'] = true;

		$request->session()->put('registerFormData', array_merge($request->session()->get('registerFormData'), $data));

		return Redirect::to(urlRouteName('register-supplier-step-3'));
	}

	public function storeStep3(Request $request)
	{
		if (!$request->session()->has('registerFormData')) {
			return redirect()->back()->with('error', __('main.errorOccurred'));
		}

		// Create subscriber model in memory but don't persist
		$subscriber = new Subscriber();
		
		// Fill the model with all session data
		$subscriber->fill([
			...$request->session()->get('registerFormData'),
			'registration_completed' => false,
			'active' => false,
			'is_provider' => true,
			'is_public' => false,
		]);
		
		// Create related models in memory
		$subscriberServices = collect();
		
		// Build services collection
		foreach ($request->session()->get('registerFormData.services') ?? [] as $element) {
			$subscriberServices->push(new SubscriberService([
				'service_id' => $element
			]));
		}
		
		// Build custom services collection
		foreach ($request->session()->get('registerFormData.custom_services') ?? [] as $element) {
			$service = new Service([
				'title' => $element,
				'type' => 'service',
			]);
			$subscriberService = new SubscriberService([
				'service_id' => null, // Will be set after service is saved
			]);
			$subscriberService->service = $service; // Store the service object
			$subscriberServices->push($subscriberService);
		}
		
		// Build service input collection
		foreach ($request->session()->get('registerFormData.service_input') ?? [] as $key => $element) {
			$existingService = $subscriberServices->first(function($ss) use ($key) {
				return $ss->service_id == $key;
			});
			
			if ($existingService) {
				$existingService->custom_value = $element;
			} else {
				$subscriberServices->push(new SubscriberService([
					'service_id' => $key,
					'custom_value' => $element
				]));
			}
		}
		
		// Build capabilities collection
		foreach ($request->session()->get('registerFormData.capabilities') ?? [] as $element) {
			$subscriberServices->push(new SubscriberService([
				'service_id' => $element
			]));
		}
		
		// Build custom capabilities collection
		foreach ($request->session()->get('registerFormData.custom_capabilities') ?? [] as $element) {
			$service = new Service([
				'title' => $element,
				'type' => 'service',
			]);
			$subscriberService = new SubscriberService([
				'service_id' => null,
			]);
			$subscriberService->service = $service; // Store the service object
			$subscriberServices->push($subscriberService);
		}
		
		// Build capability input collection
		foreach ($request->session()->get('registerFormData.capability_input') ?? [] as $key => $element) {
			$existingService = $subscriberServices->first(function($ss) use ($key) {
				return $ss->service_id == $key;
			});
			
			if ($existingService) {
				$existingService->custom_value = $element;
			} else {
				$subscriberServices->push(new SubscriberService([
					'service_id' => $key,
					'custom_value' => $element
				]));
			}
		}
		
		// Store models in session
		$request->session()->put('subscriber_model', $subscriber);
		$request->session()->put('subscriber_services', $subscriberServices);
		$request->session()->put('registerFormData.step-3-validated', true);
		
		return Redirect::to(urlRouteName('register-supplier-step-4'));
	}

	public function storeStep4(Request $request) {
		if (!$request->session()->has(['registerFormData', 'subscriber_model', 'subscriber_services'])) {
			return redirect()->back()->with('error', __('main.errorOccurred'));
		}
		
		// Get models from session
		$subscriber = $request->session()->get('subscriber_model');
		$postalCodes = collect(); // reconstruit à chaque passage (évite l'accumulation)

		$data = $request->all([
			'subscription_id',
			'zone_type',              // 'postal' (codes postaux) | 'province'
			'postal_codes',
			'subscription_state_id',  // province visée si zone_type = province
		]);

		// Zone : par code postal (1 à 10 codes) OU par province (cahier de charges).
		$zoneType = (($data['zone_type'] ?? 'postal') === 'province') ? 'province' : 'postal';
		$data['zone_type'] = $zoneType;

		$rules = ['subscription_id' => 'required'];
		if ($zoneType === 'province') {
			$rules['subscription_state_id'] = 'required|exists:states,id';
		} else {
			$rules['postal_codes'] = 'required|array';
		}

		$validator = Validator::make($data, $rules);

		$validator->setAttributeNames([
			'subscription_id'       => __('auth.register.subscription_id'),
			'postal_codes'          => __('auth.register.postal_codes'),
			'subscription_state_id' => 'Province',
		]);

		// Le forfait choisi (abonnement × catégorie × zone) doit réellement avoir un prix.
		$selectedCategory = $request->session()->get('registerFormData.service_category_id');
		$validator->after(function ($v) use ($data, $selectedCategory, $zoneType) {
			$stateId = $zoneType === 'province' ? ($data['subscription_state_id'] ?: null) : null;
			$exists = SubscriptionPrice::where('subscription_id', '=', $data['subscription_id'] ?? 0)
				->where('service_category_id', '=', $selectedCategory)
				->where('state_id', '=', $stateId)
				->exists();
			if (!$exists) {
				$v->errors()->add('subscription_id', "Ce forfait n'est pas disponible pour cette zone.");
			}

			// Zone code postal : au moins UN code postal non vide est requis
			// (« required|array » seul laisse passer 10 cases vides).
			if ($zoneType === 'postal') {
				$filled = collect($data['postal_codes'] ?? [])->filter(fn ($c) => trim((string) $c) !== '')->count();
				if ($filled < 1) {
					$v->errors()->add('postal_codes', 'Veuillez saisir au moins un code postal.');
				}
			}
		});

		if ($validator->fails()) {
			return redirect()->back()
				->withInput()
				->withErrors($validator);
		}

		if ($zoneType === 'postal') {
			foreach (($data['postal_codes'] ?? []) as $postalCode) {
				if ($postalCode) {
					$postalCodes->push(new PostalCode(['postal_code' => $postalCode]));
				}
			}
			$data['subscription_state_id'] = null;
		} else {
			$data['postal_codes'] = [];
		}

		// Update subscriber model (seul subscription_id est une colonne ici)
		$subscriber->fill(['subscription_id' => $data['subscription_id']]);

		// Store updated models back in session
		$request->session()->put('subscriber_model', $subscriber);
		$request->session()->put('postal_codes', $postalCodes);

		$data['step-4-validated'] = true;
		$request->session()->put('registerFormData', array_merge($request->session()->get('registerFormData'), $data));

		return Redirect::to(urlRouteName('register-supplier-step-5'));
	}


	public function storeStep5(Request $request) {
		if (!$request->session()->has(['registerFormData', 'subscriber_model'])) {
			return redirect()->back()->with('error', __('main.errorOccurred'));
		}

		$data = $request->all([
			'license',
			'diploma',
			'promotion',
			'image',
			'estimation',
			'job_offer',
			'url',
			'url_forfait',
			'estimation_cost',
			'accepts_cash',
			'accepts_check',
			'accepts_debit',
			'accepts_credit',
			'fr.url',
			'en.url'
		]);

		// Process any file uploads and replace UploadedFile objects with web paths
		$data = $this->processFileUploadsForSession($data, $request);

		if (empty($data['accepts_cash'])) {
			$data['accepts_cash'] = false;
		}
		if (empty($data['accepts_check'])) {
			$data['accepts_check'] = false;
		}
		if (empty($data['accepts_debit'])) {
			$data['accepts_debit'] = false;
		}
		if (empty($data['accepts_credit'])) {
			$data['accepts_credit'] = false;
		}

		$validator = Validator::make(
			$data,
			[
				'license' => 'nullable|in:on',
				'diploma' => 'nullable|in:on',
				'promotion' => 'nullable|in:on',
				'image' => 'nullable|string',  // Now expects a file path string, not UploadedFile
				'estimation' => 'nullable|in:on',
				'job_offer' => 'nullable|in:on',
				'url' => 'nullable|in:on',
			]
		);

		$validator->setAttributeNames([
			'license' =>  setting("license_title", "license_title"),
			'diploma' =>  setting("diploma_title", "diploma_title"),
			'promotion' =>  setting("promotion_title", "promotion_title"),
			'image' =>  setting("image_title", "image_title"),
			'estimation' =>  setting("estimation_title", "estimation_title"),
			'job_offer' =>  setting("job_offer_title", "job_offer_title"),
			'url' =>  setting("url_title", "url_title"),
		]);

		// Forfait site web sélectionné → un couple palier × durée valide est requis.
		$validator->after(function ($v) use ($data) {
			if (!empty($data['url']) && !\App\Support\WebsiteForfait::isValid($data['url_forfait'] ?? null)) {
				$v->errors()->add('url_forfait', 'Veuillez choisir un forfait site web.');
			}
		});

		if ($validator->fails()) {
			return redirect()->back()
				->withInput()
				->withErrors($validator);
		}

		$subscriber = $request->session()->get('subscriber_model');

		// Debug: Log the data structure before filling the model
		Log::info('Step 5 data structure:', $data);
		
		// Clean data to ensure no arrays are passed where strings are expected
		$cleanedData = $this->cleanDataForModel($data);
		
		if (!empty($cleanedData['license'])) {
			$cleanedData['profile_license_active'] = true;
			$cleanedData['profile_license_activation_datetime'] = now();
		}
		if (!empty($cleanedData['diploma'])) {
			$cleanedData['profile_diploma_active'] = true;
			$cleanedData['profile_diploma_activation_datetime'] = now();
		}
		if (!empty($cleanedData['promotion'])) {
			$cleanedData['profile_promotion_active'] = true;
			$cleanedData['profile_promotion_activation_datetime'] = now();
		}
		if (!empty($cleanedData['image'])) {
			$cleanedData['profile_image_active'] = true;
			$cleanedData['profile_image_activation_datetime'] = now();
		}
		if (!empty($cleanedData['estimation'])) {
			$cleanedData['profile_estimation_active'] = true;
			$cleanedData['profile_estimation_activation_datetime'] = now();
		}
		if (!empty($cleanedData['job_offer'])) {
			$cleanedData['profile_job_offer_active'] = true;
			$cleanedData['profile_job_offer_activation_datetime'] = now();
		}
		if (!empty($cleanedData['url'])) {
			// $cleanedData['profile_url_active'] = true; doesnt exist
			$cleanedData['profile_url_activation_datetime'] = now();
		} else {
			$cleanedData['url_forfait'] = null; // option site web non retenue
		}

		$subscriber->fill($cleanedData);
		$request->session()->put('subscriber_model', $subscriber);

		$request->session()->put('registerFormData.step-5-validated', true);

		return Redirect::to(urlRouteName('register-supplier-step-6'));
	}

	public function storeStep6(Request $request) {
		if (!$request->session()->has(['registerFormData', 'subscriber_model', 'subscriber_services', 'postal_codes'])) {
			return redirect()->back()->with('error', __('main.errorOccurred'));
		}

		$data = $request->all([
			'password',
			'password_confirmation',
			'accept_condition',
		]);

		$validator = Validator::make($data, [
			'password'              => 'required|regex:/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/',
			'password_confirmation' => 'required_with:password|same:password',
			'accept_condition'      => 'accepted',
		]);

		$validator->setAttributeNames([
			'password' => __('auth.register.password'),
			'password_confirmation' => __('auth.register.password_confirmation'),
			'accept_condition' => __('auth.register.terms'),
		]);
		
		if ($validator->fails()) {
			return redirect()->back()
				->withInput()
				->withErrors($validator);
		}

		$subscriber = $request->session()->get('subscriber_model');
		$subscriberServices = $request->session()->get('subscriber_services', collect());
		$postalCodes = $request->session()->get('postal_codes', collect());

		$subscriber->fill($data);

		// persist everything to database in a transaction
		try {
			DB::transaction(function() use ($subscriber, $subscriberServices, $postalCodes, $request) {
				$subscriber->save();
				$this->moveTemporaryFilesToFinalLocation($subscriber, $request);

				// Règle « auto-client-from-supplier » (feature #5, confirmée par Denis) :
				// chaque fournisseur (numéro F) génère un compte client jumeau (numéro C)
				// juste après, pour gonfler le décompte des membres + les tirages.
				$this->createPairedClient($subscriber);

				Auth::guard('subscribers')->loginUsingId($subscriber->id);
				Cart::empty();

				// Abonnement : prix résolu selon la CATÉGORIE + la ZONE choisie
				// (code postal = state NULL, ou la province). Corrige aussi le prix
				// par catégorie (l'ancien « premier prix » était arbitraire).
				$subscriptionItem = Cart::getItem($request->session()->get('registerFormData.subscription_id'), Subscription::class);
				if ($subscriptionItem) {
					$zoneStateId = $request->session()->get('registerFormData.subscription_state_id') ?: null;
					$resolvedCost = SubscriptionPrice::where('subscription_id', '=', $subscriptionItem->id)
						->where('service_category_id', '=', $request->session()->get('registerFormData.service_category_id'))
						->where('state_id', '=', $zoneStateId)
						->value('cost');
					if ($resolvedCost !== null) {
						$subscriptionItem->cost = (float) $resolvedCost; // honoré par Subscription::getCostAttribute
					}
					$subscriptionItem->state_id = $zoneStateId; // zone enregistrée sur l'achat (BasicCart::buyCart)
					Cart::add($subscriptionItem);
				}

				// Frais de la fiche : variables par profession (feature #6), repli sur le réglage global
				$ficheCategory = ServiceCategory::find($request->session()->get('registerFormData.service_category_id'));
				$registrationFee = $ficheCategory?->fiche_fee ?? setting('registration_fee') ?? 0;
				if ($registrationFee > 0) {
					$purchase = new Purchase();
					$purchase->fill([
						'purchase_type' => 'Registration',
						'item_name'     => 'registration_fee',
						'quantity'      => 1,
						'unit_price'    => $registrationFee,
						'total_price'   => $registrationFee,
						'applicable_tps' => true,
						'applicable_tvq' => true,
					]);
					Cart::add($purchase);
				}

				if ($subscriber->profile_license_activation_datetime) {
					$licensesData = $request->session()->get('profile_licenses', []);
					foreach ($licensesData as $licenseData) {
						$license = new License();
						$licenseData['subscriber_id'] = $subscriber->id;
						$license->saveElement($licenseData);
					}

					$purchase = new Purchase();
					$price = setting("license_price") ?? 0;
					$purchase->fill([
						'purchase_type' => 'Option profil',
						'item_name'     => 'license',
						'quantity'      => 1,
						'unit_price'    => $price,
						'total_price'   => $price,
					]);
					Cart::add($purchase);
				}

				if ($subscriber->profile_diploma_activation_datetime) {
					$diplomasData = $request->session()->get('profile_diplomas', []);
					foreach ($diplomasData as $diplomaData) {
						$diploma = new \App\Models\Diploma();
						$diplomaData['subscriber_id'] = $subscriber->id;
						$diploma->saveElement($diplomaData);
					}

					$purchase = new Purchase();
					$price = setting("diploma_price") ?? 0;
					$purchase->fill([
						'purchase_type' => 'Option profil',
						'item_name'     => 'diploma',
						'quantity'      => 1,
						'unit_price'    => $price,
						'total_price'   => $price,
					]);
					Cart::add($purchase);
				}

				if($subscriber->profile_promotion_activation_datetime) {
					$promotionsData = $request->session()->get('profile_promotions', []);
					foreach ($promotionsData as $promotionData) {
						$promotion = new Promotion();
						$promotionData['subscriber_id'] = $subscriber->id;
						$promotion->saveElement($promotionData);
					}

					$purchase = new Purchase();
					$price = setting("promotion_price") ?? 0;
					$purchase->fill([
						'purchase_type' => 'Option profil',
						'item_name'     => 'promotion',
						'quantity'      => 1,
						'unit_price'    => $price,
						'total_price'   => $price,
					]);
					Cart::add($purchase);
				}

				if ($subscriber->profile_image_activation_datetime) {
					$imagesData = $request->session()->get('profile_subscriber_images', []);
					foreach ($imagesData as $imageData) {
						$subscriberImage = new SubscriberImage();
						$imageData['subscriber_id'] = $subscriber->id;
						$subscriberImage->saveElement($imageData);
					}

					$purchase = new Purchase();
					$price = setting("image_price") ?? 0;
					$purchase->fill([
						'purchase_type' => 'Option profil',
						'item_name'     => 'image',
						'quantity'      => 1,
						'unit_price'    => $price,
						'total_price'   => $price,
					]);
					Cart::add($purchase);
				}

				if ($subscriber->profile_job_offer_activation_datetime) {
					$jobOffersData = $request->session()->get('profile_job_offers', []);
					foreach ($jobOffersData as $jobOfferData) {
						$jobOffer = new JobOffer();
						$jobOfferData['subscriber_id'] = $subscriber->id;
						$jobOffer->saveElement($jobOfferData);
					}

					$purchase = new Purchase();
					$price = setting("job_offer_price") ?? 0;
					$purchase->fill([
						'purchase_type' => 'Option profil',
						'item_name'     => 'job_offer',
						'quantity'      => 1,
						'unit_price'    => $price,
						'total_price'   => $price,
					]);
					Cart::add($purchase);
				}

				if ($subscriber->profile_estimation_activation_datetime) {
					$purchase = new Purchase();
					$price = setting("estimation_price") ?? 0;
					$purchase->fill([
						'purchase_type' => 'Option profil',
						'item_name'     => 'estimation',
						'quantity'      => 1,
						'unit_price'    => $price,
						'total_price'   => $price,
					]);
					Cart::add($purchase);
				}

				if ($subscriber->profile_url_activation_datetime) {
					$purchase = new Purchase();
					// Forfait site web : prix du couple palier × durée choisi (repli sur url_price).
					$price = \App\Support\WebsiteForfait::price($subscriber->url_forfait) ?? (setting("url_price") ?? 0);
					$purchase->fill([
						'purchase_type' => 'Option profil',
						'item_name'     => 'url',
						'quantity'      => 1,
						'unit_price'    => $price,
						'total_price'   => $price,
					]);
					Cart::add($purchase);
				}

				// Save subscriber services
				foreach ($subscriberServices as $subscriberService) {
					// Set subscriber_id
					$subscriberService->subscriber_id = $subscriber->id;

					// If it has a custom service, save that first
					if (isset($subscriberService->service)) {
						$subscriberService->service->save();
						$subscriberService->service_id = $subscriberService->service->id;
						unset($subscriberService->service);
					}

					$subscriberService->save();
				}

				// Save postal codes
				foreach ($postalCodes as $postalCode) {
					$postalCode->subscriber_id = $subscriber->id;
					$postalCode->save();
				}
			}); // end transaction

			// Clear session data including profile option data
			$request->session()->forget([
				'subscriber_model', 
				'subscriber_services', 
				'postal_codes',
				'registerFormData',
				'profile_licenses',
				'profile_diplomas',
				'profile_promotions',
				'profile_subscriber_images',
				'profile_job_offers'
			]);

			$subscriber->sendMail(
				'register',
				['url' => urlRouteName('subscriber.validate', ['token' => $subscriber->recoveringToken()], true)]
			);

			return Redirect::to(urlRouteName('cart'))->with('success', __('cart.item.added'));

		} catch (\Exception $e) {
			Auth::guard('subscribers')->logout();
			\Log::error('Registration failed: ' . $e->getMessage());
			return redirect()->back()
				->withInput()
				->with('error', __('main.errorOccurred'));
		}
	}

	/**
	 * Helper method to extract existing subscriber service data
	 */
	private function extractSubscriberServiceData($subscriber) {
		$subscriberServices = $subscriber->subscriberServices()->with('service')->get();

		// Separate services and capabilities
		$serviceRelations = $subscriberServices->filter(function($ss) {
			return $ss->service && $ss->service->type === 'service';
		});
		
		$capabilityRelations = $subscriberServices->filter(function($ss) {
			return $ss->service && $ss->service->type === 'capability';
		});

		return [
			'existingServices' => $serviceRelations
				->whereNotNull('service_id')
				->pluck('service_id')
				->toArray(),
				
			'existingServiceInputs' => $serviceRelations
				->whereNotNull('custom_value')
				->pluck('custom_value', 'service_id')
				->toArray(),
				
			'existingCustomServices' => $serviceRelations
				->whereNull('service_id')
				->pluck('service.title')
				->filter()
				->toArray(),
				
			'existingCapabilities' => $capabilityRelations
				->whereNotNull('service_id')
				->pluck('service_id')
				->toArray(),
				
			'existingCapabilityInputs' => $capabilityRelations
				->whereNotNull('custom_value')
				->pluck('custom_value', 'service_id')
				->toArray(),
				
			'existingCustomCapabilities' => $capabilityRelations
				->whereNull('service_id')
				->pluck('service.title')
				->filter()
				->toArray(),
		];
	}

	/**
	 * Process file uploads following the platform's media handling approach
	 * 
	 * @param array $data
	 * @param Request $request
	 * @return array
	 */
	private function processFileUploadsForSession(array $data, Request $request): array
	{
		$sessionId = $request->session()->getId();
		
		foreach ($data as $key => $value) {
			if ($value instanceof \Illuminate\Http\UploadedFile) {
				// Store the file temporarily following platform's approach
				$webPath = $this->saveTempFile($value, $sessionId, $key);
				$data[$key] = $webPath;
			} elseif (is_array($value)) {
				// Handle arrays that might contain UploadedFile objects
				$processedArray = [];
				foreach ($value as $arrayKey => $item) {
					if ($item instanceof \Illuminate\Http\UploadedFile) {
						$webPath = $this->saveTempFile($item, $sessionId, $key);
						$processedArray[$arrayKey] = $webPath;
					} else {
						$processedArray[$arrayKey] = $item;
					}
				}
				$data[$key] = $processedArray;
			}
			// Handle translation fields like 'fr.url', 'en.url' - keep them as strings
			// No special processing needed for these
		}
		
		return $data;
	}

	/**
	 * Move temporary files to their final locations after registration is complete
	 * 
	 * @param Subscriber $subscriber
	 * @param Request $request
	 * @return void
	 */
	private function moveTemporaryFilesToFinalLocation(Subscriber $subscriber, Request $request): void
	{
		$sessionId = $request->session()->getId();
		$tempDirectory = "temp/registration/{$sessionId}";
		
		// Handle files stored in subscriber model during step 5
		$subscriberData = $request->session()->get('subscriber_model');
		if ($subscriberData) {
			$fieldsToCheck = ['image']; // Add other fields that might contain file paths
			
			foreach ($fieldsToCheck as $field) {
				if (!empty($subscriberData->$field) && is_string($subscriberData->$field)) {
					$tempPath = $subscriberData->$field;
					
					if (str_starts_with($tempPath, $tempDirectory)) {
						// This is a temporary file, move it to final location
						$finalPath = $this->moveFileToFinalLocation($tempPath, $subscriber, $field);
						
						// Update the subscriber with the final path
						$subscriber->update([$field => $finalPath]);
					}
				}
			}
		}
		
		// Handle profile options stored in session
		$profileOptionTypes = ['licenses', 'promotions', 'subscriber_images', 'job_offers'];
		
		foreach ($profileOptionTypes as $optionType) {
			$sessionKey = "profile_{$optionType}";
			$optionData = $request->session()->get($sessionKey, []);
			
			foreach ($optionData as &$item) {
				if (isset($item['image']) && is_string($item['image']) && str_starts_with($item['image'], $tempDirectory)) {
					// Move temporary file to final location
					$item['image'] = $this->moveFileToFinalLocation($item['image'], $subscriber, 'image');
				}
				
				// Handle any other file fields as needed
				foreach ($item as $key => &$value) {
					if (is_string($value) && str_starts_with($value, $tempDirectory)) {
						$value = $this->moveFileToFinalLocation($value, $subscriber, $key);
					}
				}
			}
			
			// Update session with final paths (though this will be cleared soon anyway)
			$request->session()->put($sessionKey, $optionData);
		}
		
		// Clean up temporary directory
		$this->cleanupTemporaryFiles($sessionId);
	}

	/**
	 * Move a single file from temporary location to final location
	 * 
	 * @param string $tempPath
	 * @param Subscriber $subscriber
	 * @param string $tag
	 * @return string Final file path
	 */
	private function moveFileToFinalLocation(string $tempPath, Subscriber $subscriber, string $tag): string
	{
		$storage = Storage::disk('public');
		
		if (!$storage->exists($tempPath)) {
			return $tempPath; // File doesn't exist, return original path
		}
		
		try {
			// Get the file content
			$fileContent = $storage->get($tempPath);
			$filename = basename($tempPath);
			
			// Create a temporary UploadedFile-like object to use with saveMedia
			$tempFile = tmpfile();
			fwrite($tempFile, $fileContent);
			$metadata = stream_get_meta_data($tempFile);
			$tmpPath = $metadata['uri'];
			
			// Create an UploadedFile instance
			$uploadedFile = new \Illuminate\Http\UploadedFile(
				$tmpPath,
				$filename,
				mime_content_type($tmpPath),
				null,
				true
			);
			
			// Use the existing saveMedia system
			$finalPath = $subscriber->saveMedia($uploadedFile, $tag, 'single');
			
			// Clean up
			fclose($tempFile);
			
			return $finalPath;
			
		} catch (Exception $e) {
			// If moving fails, log the error and return the temp path
			Log::error("Failed to move temporary file {$tempPath}: " . $e->getMessage());
			return $tempPath;
		}
	}

	/**
	 * Clean up temporary files after registration
	 * 
	 * @param string $sessionId
	 * @return void
	 */
	private function cleanupTemporaryFiles(string $sessionId): void
	{
		try {
			$storage = Storage::disk('public');
			$tempDirectory = "temp/registration/{$sessionId}";
			
			if ($storage->exists($tempDirectory)) {
				$storage->deleteDirectory($tempDirectory);
			}
		} catch (Exception $e) {
			Log::error("Failed to cleanup temporary files for session {$sessionId}: " . $e->getMessage());
		}
	}

	/**
	 * Save temporary file following platform's MediaTrait approach
	 * 
	 * @param \Illuminate\Http\UploadedFile $file
	 * @param string $sessionId
	 * @param string $tag
	 * @return string Web path to the file
	 */
	private function saveTempFile(\Illuminate\Http\UploadedFile $file, string $sessionId, string $tag): string
	{
		// Follow MediaTrait setup pattern
		$public_path = rtrim(config('media.public_path', public_path()), '/\\') . '/';
		$files_directory = rtrim(ltrim(config('media.files_directory', 'medias'), '/\\'), '/\\') . '/';
		
		// Create temp directory structure following platform's pattern
		$directory_uri = 'temp/registration/' . $sessionId . '/';
		$full_directory = $public_path . $files_directory . $directory_uri;
		
		// Create directory if it doesn't exist
		if (!File::isDirectory($full_directory)) {
			File::makeDirectory($full_directory, 0755, true);
		}
		
		// Generate unique filename following platform's approach
		$filename = uniqid() . '_' . $file->getClientOriginalName();
		
		// Move file to temporary location
		$file->move($full_directory, $filename);
		
		// Return web-accessible path following MediaTrait pattern
		return '/' . $files_directory . $directory_uri . $filename;
	}

	/**
	 * Clean data structure to ensure no arrays are passed where strings are expected
	 * 
	 * @param array $data
	 * @return array
	 */
	private function cleanDataForModel(array $data): array
	{
		$cleaned = [];
		
		foreach ($data as $key => $value) {
			// If it's an array but shouldn't be, extract the first string value
			if (is_array($value)) {
				Log::warning("Found array for field {$key}, converting to string:", $value);
				
				// Try to get the first valid string value from the array
				$stringValue = '';
				foreach ($value as $item) {
					if (is_string($item) && !empty($item)) {
						$stringValue = $item;
						break;
					}
				}
				$cleaned[$key] = $stringValue;
			} else {
				$cleaned[$key] = $value;
			}
		}
		
		Log::info('Cleaned data structure:', $cleaned);
		return $cleaned;
	}
}
