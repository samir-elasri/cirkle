<?php

namespace App\Http\Controllers;

use App\Models\Core\Subscriber;
use App\Models\JobOffer;
use App\Models\License;
use App\Models\Promotion;
use App\Models\SubscriberImage;
use Arr;
use Auth;
use DB;
use Illuminate\Http\Request;
use ModelUtility as ModelUtilityFacade;
use Str;
use View;
use Storage;
use Log;
use File;

class ProfileOptionController extends Controller
{
	private function getSubscriber() {
		if (logged_in()) {
			return Auth::guard('subscribers')->user();
		}
		return request()->session()->get('subscriber_model');
	}

	public function option($params, Request $request)
	{
		$optionName = $request->route()->getName();
		$params['optionTitle'] = setting("{$optionName}_title");
		$params['optionDescription'] = setting("{$optionName}_description");
		$params['optionName'] = $optionName;

		if (!logged_in()) {
			return restricted($params);
		}

		$params['optionData'] = $this->getData($optionName);

		return $params;
	}

	private function getData($optionName)
	{
		$data = null;
		$sub = $this->getSubscriber();

		switch ($optionName) {
			case 'estimation':
			case 'url':
				$data = $sub;
				break;
			default:
				$relation = Str::camel(Str::plural($optionName, 2));
				if ($sub->id) {
					// Subscriber exists in database, get relations normally
					$data = $sub->$relation;
				} else {
					// Subscriber is from session, get data from session and convert to models
					$sessionKey = 'profile_' . $optionName;
					$rawData = request()->session()->get($sessionKey, []);
					$class = ModelUtilityFacade::getClassByCollectionName($optionName);
					$data = collect($rawData)->map(function($item) use ($class) {
						$model = new $class();
						$model->fill($item);
						return $model;
					});
				}
		}

		return $data;
	}

	public function add(Request $request, $type = null)
	{
		$data = $request->all();
		$class = ModelUtilityFacade::getClassByCollectionName($type);
		// Soumission NON-JS (form avec ?redirect=1) : on revient à la page au lieu d'un JSON.
		$redirect = (bool) $request->query('redirect');

		// Robustesse traductions : un groupe à UN seul champ (ex. diplôme = fr[title] seul)
		// peut arriver indexé numériquement (fr => [0 => valeur]) au lieu de fr => ['title' => …],
		// ce qui faisait insérer une colonne « 0 » et plantait l'inscription. On re-clé par
		// position sur les attributs traduisibles du modèle.
		if ($class) {
			$translatable = (array) ((new $class)->translatedAttributes ?? []);
			foreach (['fr', 'en'] as $loc) {
				if ($translatable && isset($data[$loc]) && is_array($data[$loc]) && $data[$loc] !== []) {
					$keys = array_keys($data[$loc]);
					if ($keys === range(0, count($keys) - 1)) { // purement numérique
						$data[$loc] = array_combine(
							array_slice($translatable, 0, count($data[$loc])),
							array_values($data[$loc])
						);
					}
				}
			}
		}

		$sub = $this->getSubscriber();
		if (!$sub) {
			return response()->json(['error' => 'No subscriber found'], 400);
		}

		if ($sub->id) {
			// Subscriber exists in database, save normally
			$data['subscriber_id'] = $sub->id;
			$element = new $class;

			if (Arr::get($data, 'is_photos')) {
				foreach (Arr::get($data, 'images') as $image) {
					$element = new $class;
					$element->saveElement([
						'image' => $image,
						'subscriber_id' => $sub->id,
						'legend' => Arr::get($data, 'legend'),
					]);
				}

				if ($redirect) { return back(); }
				return response()->json(['data' => $data]);
			}

			$element->saveElement($data);
			if ($redirect) { return back(); }
			return response()->json(['data' => $data]);
		} else {
			// Subscriber is from session, process file uploads and store processed data.
			// Certains types arrivent au SINGULIER (license, diploma) alors que les
			// lecteurs (getXxxList, storeStep6) lisent la clé au PLURIEL : on aligne,
			// sinon l'élément ajouté part dans une clé morte (jamais affiché ni sauvé).
			$sessionKeyMap = ['license' => 'profile_licenses', 'diploma' => 'profile_diplomas'];
			$sessionKey = $sessionKeyMap[$type] ?? ('profile_' . $type);
			$existingData = array_values((array) request()->session()->get($sessionKey, []));

			// Process file uploads and replace UploadedFile objects with web paths
			$processedData = $this->processFileUploadsForSession($data, $request);
			unset($processedData['session_index'], $processedData['_token']);

			if (Arr::get($data, 'is_photos')) {
				// Photos : maximum 12 par fournisseur (cahier de charges).
				$incoming = Arr::get($processedData, 'images', []);
				if (count($existingData) + count($incoming) > 12) {
					return response()->json(['error' => 'Maximum 12 photos.'], 422);
				}
				foreach ($incoming as $imagePath) {
					$existingData[] = [
						'image' => $imagePath,
						'legend' => Arr::get($processedData, 'legend'),
					];
				}
			} elseif (($idx = (int) $request->input('session_index')) >= 1 && isset($existingData[$idx - 1])) {
				// MODIFICATION d'un item de session (inscription 1 page) : session_index
				// = position+1 de l'item à remplacer.
				$existingData[$idx - 1] = $processedData;
			} else {
				$existingData[] = $processedData;
			}

			request()->session()->put($sessionKey, $existingData);
			if ($redirect) { return back(); }
			return response()->json(['data' => $processedData, 'items' => $existingData]);
		}
	}

	public function editLicenses($params, Request $request)
	{
		$params['data'] = $this->edit('licenses');
		return $params;
	}

	public function editDiplomas($params, Request $request)
	{
		$params['data'] = $this->edit('diplomas');
		return $params;
	}

	public function editPromotions($params, Request $request)
	{
		$params['data'] = $this->edit('promotions');
		return $params;
	}

	public function editPhotos($params, Request $request)
	{
		$params['data'] = $this->edit('photos');
		return $params;
	}

	public function editEstimations($params, Request $request)
	{
		$params['data'] = $this->edit('estimations');
		return $params;
	}

	public function editJobOffers($params, Request $request)
	{
		$params['data'] = $this->edit('jobOffers');
		return $params;
	}

	public function editUrl($params, Request $request)
	{
		$params['data'] = $this->edit('url');
		return $params;
	}

	private function edit($type)
	{
		$sub = $this->getSubscriber();

		if (!$sub) {
			abort(404);
		}

		switch ($type) {
			case 'licenses':
				if ($sub->id) {
					return $sub->licenses->sortBy('position');
				} else {
					$rawData = request()->session()->get('profile_licenses', []);
					return collect($rawData)->map(function($data) {
						$license = new License();
						$license->fill($data);
						return $license;
					});
				}
			case 'diplomas':
				if ($sub->id) {
					return $sub->diplomas->sortBy('position');
				} else {
					$rawData = request()->session()->get('profile_diplomas', []);
					return collect($rawData)->map(function($data) {
						$diploma = new \App\Models\Diploma();
						$diploma->fill($data);
						return $diploma;
					});
				}
			case 'promotions':
				if ($sub->id) {
					return $sub->promotions->sortBy('position');
				} else {
					$rawData = request()->session()->get('profile_promotions', []);
					return collect($rawData)->map(function($data) {
						$promotion = new Promotion();
						$promotion->fill($data);
						return $promotion;
					});
				}
			case 'photos':
				if ($sub->id) {
					return $sub->subscriberImages->sortBy('position');
				} else {
					$rawData = request()->session()->get('profile_subscriber_images', []);
					return collect($rawData)->map(function($data) {
						$image = new SubscriberImage();
						$image->fill($data);
						return $image;
					});
				}
			case 'estimations':
				return $sub;
			case 'jobOffers':
				if ($sub->id) {
					return $sub->jobOffers->sortBy('position');
				} else {
					$rawData = request()->session()->get('profile_job_offers', []);
					return collect($rawData)->map(function($data) {
						$jobOffer = new JobOffer();
						$jobOffer->fill($data);
						return $jobOffer;
					});
				}
			case 'url':
				return $sub;
		}
	}

	public function getJobOfferList(Request $request)
	{
		$sub = $this->getSubscriber();
		if ($sub) {
			if ($sub->id) {
				// Logged in subscriber - query database
				$jobOffers = JobOffer::where('subscriber_id', $sub->id)->get();
			} else {
				// Session subscriber - convert raw data to models for display
				$rawData = request()->session()->get('profile_job_offers', []);
				$jobOffers = collect($rawData)->map(function($data) {
					$jobOffer = new JobOffer();
					$jobOffer->fill($data);
					return $jobOffer;
				});
			}
			
			$view = View::make('partials.profile-options.job-offer-list')->with(['data' => $jobOffers])->render();
			return response()->json(['view' => $view], 200);
		}
		return response()->json([], 200);
	}

	public function getLicenseList(Request $request)
	{
		$sub = $this->getSubscriber();
		if ($sub) {
			if ($sub->id) {
				// Logged in subscriber - query database
				$licenses = License::where('subscriber_id', $sub->id)->orderBy('position')->get();
			} else {
				// Session subscriber - convert raw data to models for display
				$rawData = request()->session()->get('profile_licenses', []);
				$licenses = collect($rawData)->map(function($data) {
					$license = new License();
					$license->fill($data);
					return $license;
				});
			}
			
			$view = View::make('partials.profile-options.license-list')->with(['data' => $licenses])->render();
			return response()->json(['view' => $view, 'licenses' => $licenses], 200);
		}
		return response()->json([], 200);
	}

	public function getDiplomaList(Request $request)
	{
		$sub = $this->getSubscriber();
		if ($sub) {
			if ($sub->id) {
				$diplomas = \App\Models\Diploma::where('subscriber_id', $sub->id)->orderBy('position')->get();
			} else {
				$rawData = request()->session()->get('profile_diplomas', []);
				$diplomas = collect($rawData)->map(function($data) {
					$diploma = new \App\Models\Diploma();
					$diploma->fill($data);
					return $diploma;
				});
			}

			$view = View::make('partials.profile-options.diploma-list')->with(['data' => $diplomas])->render();
			return response()->json(['view' => $view, 'data' => $diplomas], 200);
		}
		return response()->json([], 200);
	}

	public function getPromotionList(Request $request)
	{
		$sub = $this->getSubscriber();
		if ($sub) {
			if ($sub->id) {
				// Logged in subscriber - query database
				$promotions = Promotion::where('subscriber_id', $sub->id)->orderBy('position')->get();
			} else {
				// Session subscriber - convert raw data to models for display
				$rawData = request()->session()->get('profile_promotions', []);
				$promotions = collect($rawData)->map(function($data) {
					$promotion = new Promotion();
					$promotion->fill($data);
					return $promotion;
				});
			}
			
			$view = View::make('partials.profile-options.promotion-list')->with(['data' => $promotions])->render();
			return response()->json(['view' => $view, 'data' => $promotions], 200);
		}
		return response()->json([], 200);
	}

	public function getPhotoList(Request $request)
	{
		$sub = $this->getSubscriber();
		if ($sub) {
			if ($sub->id) {
				// Logged in subscriber - query database
				$images = SubscriberImage::where('subscriber_id', $sub->id)->orderBy('position')->get();
			} else {
				// Session subscriber - convert raw data to models for display
				$rawData = request()->session()->get('profile_subscriber_images', []);
				$images = collect($rawData)->map(function($data) {
					$image = new SubscriberImage();
					$image->fill($data);
					return $image;
				});
			}
			
			$view = View::make('partials.profile-options.photo-list')->with(['data' => $images])->render();
			return response()->json(['view' => $view], 200);
		}
		return response()->json([], 200);
	}

	public function deleteOption(Request $request, $type = null, $id = null)
	{
		$sub = $this->getSubscriber();
		if ($sub && $id) {
			if ($sub->id) {
				// Logged in subscriber - delete from database
				$element = DB::table($type)->where('id', $id)->where('subscriber_id', $sub->id)->delete();
			} else {
				// Inscription en cours : les items vivent dans les clés de session
				// « profile_* » (pas sur le modèle — l'ancien code rejetait sur une
				// relation vide et ne supprimait jamais rien). L'id reçu est la
				// POSITION+1 dans la liste (pseudo-id des items de session).
				$sessionKeyMap = [
					'license' => 'profile_licenses', 'licenses' => 'profile_licenses',
					'diploma' => 'profile_diplomas', 'diplomas' => 'profile_diplomas',
				];
				$sessionKey = $sessionKeyMap[$type] ?? ('profile_' . $type);
				$items = array_values((array) request()->session()->get($sessionKey, []));
				$idx = (int) $id - 1;
				if (isset($items[$idx])) {
					array_splice($items, $idx, 1);
					request()->session()->put($sessionKey, $items);
				}
				if (!$request->query('redirect')) {
					// La page unique se resynchronise sur la liste restante.
					return response()->json(['items' => $items], 200);
				}
			}
			// Suppression sans JS (lien avec ?redirect=1, ex. photos dont le bouton JS gelé
			// ne marchait pas) : on revient à la page avec un message au lieu d'un JSON.
			if ($request->query('redirect')) {
				return back();
			}
			return response()->json([], 200);
		}
		if ($request->query('redirect')) {
			return back();
		}
		return response()->json([], 200);
	}

	/**
	 * Modifier un item d'option (permis, diplôme, …) déjà enregistré.
	 * Soumission classique (form dans une modale) — pas d'AJAX, le composant JS
	 * compilé ne gère que add/delete/move. Vérifie que l'item appartient bien au
	 * fournisseur connecté avant de mettre à jour (titre/description + champs propres).
	 */
	public function update(Request $request, $type = null, $id = null)
	{
		$sub = $this->getSubscriber();
		if (!$sub || !$sub->id || !$id) {
			return redirect()->back()->with('error', __('main.errorOccurred'));
		}

		$class = ModelUtilityFacade::getClassByCollectionName($type);
		if (!$class) {
			abort(404);
		}

		$item = $class::find($id);
		if (!$item || (int) $item->subscriber_id !== (int) $sub->id) {
			abort(403);
		}

		$data = $request->except(['_token', '_method']);
		$data['subscriber_id'] = $sub->id;
		$item->saveElement($data);

		return redirect()->back()->with('success', __('profile.options.updated'));
	}

	public function moveOption(Request $request, $type = null, $id = null, $direction = null)
	{
		if (!$id || !$direction) {
			return response()->json([], 200);
		}

		$sub = $this->getSubscriber();
		if (!$sub) {
			return response()->json([], 200);
		}

		if ($sub->id) {
			// Logged in subscriber - handle normally with database
			$class = ModelUtilityFacade::getClassByCollectionName($type);

			$element = $class::find($id);
			if ($direction === 'up') {
				$next = $class::where('subscriber_id', '=', $element->subscriber_id)->where('position', '<', $element->position)
					->orderByDesc('position')->first();
			}
			else {
				$next = $class::where('subscriber_id', '=', $element->subscriber_id)->where('position', '>', $element->position)
					->orderBy('position')->first();
			}

			if (!$next) {
					if ($request->query('redirect')) { return back(); }
				return response()->json(['success' => true], 200);
			}

			$nextPosition = $next->position;
			$next->position = $element->position;
			$element->position = $nextPosition;
			$element->save();
			$next->save();
		} else {
			// Session subscriber - manipulate collection and update session
			$relationName = Str::camel(Str::plural($type, 2));
			if (isset($sub->$relationName)) {
				$collection = $sub->$relationName;
				$elementIndex = $collection->search(function($item) use ($id) {
					return $item->id == $id;
				});
				
				if ($elementIndex !== false) {
					$element = $collection->get($elementIndex);
					$targetIndex = $direction === 'up' ? $elementIndex - 1 : $elementIndex + 1;
					
					if ($targetIndex >= 0 && $targetIndex < $collection->count()) {
						$target = $collection->get($targetIndex);
						$tempPosition = $element->position;
						$element->position = $target->position;
						$target->position = $tempPosition;
						
						request()->session()->put('subscriber_model', $sub);
					}
				}
			}
		}

		if ($request->query('redirect')) { return back(); }
		return response()->json(['success' => true], 200);
	}

	function promotionInProgressToggle(Request $request, $id)
	{
		$sub = $this->getSubscriber();
		if (!$sub) {
			return response()->json([], 200);
		}

		if ($sub->id) {
			// Logged in subscriber
			$promotion = Promotion::find($id);
			if ($promotion && $promotion->subscriber_id == $sub->id) {
				$promotion->saveElement(['in_progress' => !$promotion->in_progress]);
			}
		} else {
			// Session subscriber
			if (isset($sub->promotions)) {
				$promotion = $sub->promotions->firstWhere('id', $id);
				if ($promotion) {
					$promotion->in_progress = !$promotion->in_progress;
					request()->session()->put('subscriber_model', $sub);
				}
			}
		}

		if ($request->query('redirect')) { return back(); }
		return response()->json([], 200);
	}

	function jobOfferActiveToggle(Request $request, $id)
	{
		$sub = $this->getSubscriber();
		if (!$sub) {
			return response()->json([], 200);
		}

		if ($sub->id) {
			// Logged in subscriber
			$jobOffer = JobOffer::find($id);
			if ($jobOffer && $jobOffer->subscriber_id == $sub->id) {
				$jobOffer->saveElement(['currently_recruiting' => !$jobOffer->currently_recruiting]);
			}
		} else {
			// Session subscriber
			if (isset($sub->jobOffers)) {
				$jobOffer = $sub->jobOffers->firstWhere('id', $id);
				if ($jobOffer) {
					$jobOffer->currently_recruiting = !$jobOffer->currently_recruiting;
					request()->session()->put('subscriber_model', $sub);
				}
			}
		}

		if ($request->query('redirect')) { return back(); }
		return response()->json([], 200);
	}

	function editLegend(Request $request, $id = null)
	{
		$data = $request->all();
		$sub = $this->getSubscriber();

		if ($sub) {
			if ($sub->id) {
				// Logged in subscriber
				$image = SubscriberImage::where('subscriber_id', $sub->id)->where('id', $id)->first();
				if ($image) {
					$image->saveElement($data);
				}
			} else {
				// Session subscriber
				if (isset($sub->subscriberImages)) {
					$image = $sub->subscriberImages->firstWhere('id', $id);
					if ($image) {
						foreach ($data as $key => $value) {
							$image->$key = $value;
						}
						request()->session()->put('subscriber_model', $sub);
					}
				}
			}
		}
	}

	function updateEstimations(Request $request) {
		$data = $request->all();

		$data['accepts_cash'] = Arr::has($data, 'accepts_cash');
		$data['accepts_check'] = Arr::has($data, 'accepts_check');
		$data['accepts_debit'] = Arr::has($data, 'accepts_debit');
		$data['accepts_credit'] = Arr::has($data, 'accepts_credit');

		$sub = $this->getSubscriber();
		if ($sub) {
			if ($sub->id) {
				// Subscriber exists in database, save normally
				$subscriber = Subscriber::find($sub->id);
				$subscriber->saveElement($data, true);
			} else {
				// Subscriber is from session, update model directly
				foreach ($data as $key => $value) {
					$sub->$key = $value;
				}
				// Update session with modified subscriber
				request()->session()->put('subscriber_model', $sub);
			}
		}

		return back()->withInput()->with(['success' => trans('providers.updated-success')]);
	}

	function updateUrl(Request $request) {
		$data = $request->all();

		$sub = $this->getSubscriber();
		if ($sub) {
			if ($sub->id) {
				// Subscriber exists in database, save normally
				$subscriber = Subscriber::find($sub->id);
				$subscriber->saveElement($data, true);
			} else {
				// Subscriber is from session, update model directly
				foreach ($data as $key => $value) {
					$sub->$key = $value;
				}
				// Update session with modified subscriber
				request()->session()->put('subscriber_model', $sub);
			}
		}

		return back()->withInput()->with(['success' => trans('providers.updated-success')]);
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
		$processedData = [];
		
		foreach ($data as $key => $value) {
			if ($value instanceof \Illuminate\Http\UploadedFile) {
				// Handle single file upload
				$webPath = $this->saveTempFile($value, $sessionId, $key);
				$processedData[$key] = $webPath;
			} elseif (is_array($value) && !empty($value)) {
				// Tableaux de fichiers (images[]) OU groupes de traductions (fr[title], …).
				// IMPORTANT : préserver les CLÉS — l'ancien « $arr[] = $item » ré-indexait
				// fr['title'] en fr[0], perdant titres/descriptions de tous les items de
				// session (la vraie cause du « indexé numériquement » contourné dans add()).
				$processedArray = [];
				foreach ($value as $k => $item) {
					if ($item instanceof \Illuminate\Http\UploadedFile) {
						$webPath = $this->saveTempFile($item, $sessionId, $key);
						$processedArray[$k] = $webPath;
					} else {
						$processedArray[$k] = $item;
					}
				}
				$processedData[$key] = $processedArray;
			} else {
				// Keep non-file data as is
				$processedData[$key] = $value;
			}
		}
		
		return $processedData;
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
}
