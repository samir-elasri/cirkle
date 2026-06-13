<?php

namespace App\Http\Controllers;

use App\Models\ContactedProvider;
use App\Models\Core\Country;
use App\Models\Core\State;
use App\Models\Core\Subscriber;
use App\Models\LikedSubscriber;
use App\Models\PostalCode;
use App\Models\SavedSearch;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SubscriberService;
use App\Models\SubscriberServiceCategory;
use Arr;
use Illuminate\Http\Request;
use Intervention\Image\Exception\NotReadableException;
use Validator;

class ProviderController extends Controller
{
    public function show($params, Request $request, $id)
    {
        $provider = Subscriber::where('active', '=', true)
            ->where('id', '=', $id)
            ->firstOrFail();

        // Historique des consultations (feature #11) : un client connecté consulte une fiche.
        // On évite les doublons consécutifs (rafraîchissements).
        $viewer = auth('subscribers')->user();
        if ($viewer && (int) $viewer->id !== (int) $provider->id) {
            $last = \App\Models\ConsultationHistory::where('subscriber_id', $viewer->id)
                ->latest()->first();
            if (!$last || (int) $last->viewed_subscriber_id !== (int) $provider->id) {
                \App\Models\ConsultationHistory::create([
                    'subscriber_id' => $viewer->id,
                    'viewed_subscriber_id' => $provider->id,
                ]);
            }
        }

        $subcategories = $provider->subscriberServiceCategories()
            ->with('serviceCategory')
            ->get();

        $services = $provider->subscriberServices()
            ->whereHas('service',  function ($query) {
                $query->where('active', '=', true)
                    ->where('type', '=', 'service');
            })
            ->with(['service'])
            ->get()
            ->sortBy(fn ($row) => $row->service->source_row ?? PHP_INT_MAX)
            ->values();

        $capabilities = $provider->subscriberServices()
            ->whereHas('service',  function ($query) {
                $query->where('active', '=', true)
                    ->where('type', '=', 'capability');
            })
            ->with(['service'])
            ->get()
            ->sortBy(fn ($row) => $row->service->source_row ?? PHP_INT_MAX)
            ->values();

        // Sauts de bloc du fichier MASTER (toutes lignes de la fiche, cochées ou non) :
        // un saut reste visible même si la ligne qui le portait n'est pas cochée.
        $gapRows = $provider->service_category_id
            ? Service::where('service_category_id', $provider->service_category_id)
                ->where('gap_before', true)
                ->pluck('source_row')
                ->filter()
                ->all()
            : [];

        $promotions = collect();
        if ($provider->profile_promotion_active) {
            $promotions = $provider
                ->promotions()
                ->where('in_progress', '=', true)
                ->get()
                ->sortBy('title');
        }

        $licenses = collect();
        if ($provider->profile_license_active) {
            $licenses = $provider->licenses()
                ->get()
                ->sortBy('title');
        }

        // Diplômes (option PDIPOMECK, feature #9) : affichés si l'option est active.
        $diplomas = collect();
        if ($provider->profile_diploma_active) {
            $diplomas = $provider->diplomas()
                ->orderBy('position')
                ->get();
        }

        $images = collect();
        if ($provider->profile_image_active) {
            $images = $provider->subscriberImages()
                ->orderBy('position')
                ->get();
        }

        $jobOffers = collect();
        if ($provider->profile_job_offer_active) {
            $jobOffers = $provider->jobOffers()
                ->where('currently_recruiting', '=', true)
                ->get()
                ->sortBy('title');
        }

        $evaluations = $provider->receivedEvaluations()
            ->where('active', '=', true)
            ->where(function ($query) {
                $query->where('insulting', '=', false)
                    ->orWhere('validated', '=', true);
            })
            ->orderByDesc('created_at')
            ->with('client')
            ->get();

        $title = '';

        return array_merge($params, compact(
            'title',
            'provider',
            'subcategories',
            'services',
            'capabilities',
            'gapRows',
            'promotions',
            'licenses',
            'diplomas',
            'images',
            'jobOffers',
            'evaluations',
        ));
    }

    public function like(Request $request) {
        $like = $request->input('like');
        $providerId = $request->input('id');
        $subscriber = auth('subscribers')->user();

        if (!$subscriber) {
            abort(400);
        }

        if ($like) {
            LikedSubscriber::firstOrCreate([
                'subscriber_id' => $subscriber->id,
                'liked_subscriber_id' => $providerId,
            ]);
        }
        else {
            LikedSubscriber::where('subscriber_id', $subscriber->id)
                ->where('liked_subscriber_id', '=', $providerId)
                ->delete();
        }

        return response()->noContent();
    }

    /**
     * Cœur « favori profession » (feature #11). Réutilise le composant JS `like`
     * (générique : url + id), pointé sur cette route avec l'id de la profession.
     */
    public function likeProfession(Request $request) {
        $like = $request->input('like');
        $professionId = $request->input('id');
        $subscriber = auth('subscribers')->user();

        if (!$subscriber) {
            abort(400);
        }

        if ($like) {
            \App\Models\LikedProfession::firstOrCreate([
                'subscriber_id' => $subscriber->id,
                'service_category_id' => $professionId,
            ]);
        } else {
            \App\Models\LikedProfession::where('subscriber_id', $subscriber->id)
                ->where('service_category_id', '=', $professionId)
                ->delete();
        }

        return response()->noContent();
    }

    public function contact(Request $request) {
        $client = auth('subscribers')->user();
        $provider = Subscriber::find($request->input('provider_id'));
        $text = $request->input('text');

        if (!$client || !$provider || !$text) {
            abort(400);
        }

        $success = $provider->sendMail(
            'new_contact_request',
            compact('text'),
            [['name' => $client->name, 'address' => $client->email]]
        );

        if ($success) {
            ContactedProvider::create([
                'client_id' => $client->id,
                'provider_id' => $provider->id,
            ]);

            return redirect()
                ->back()
                ->with('success', __('providers.contact-this-provider.success'));
        }

        return redirect()
            ->back()
            ->with('error', __('providers.contact-this-provider.error'));
    }

    public function edit($params, Request $request) {

        $provider = auth('subscribers')->user();

        $states = State::where('active', '=', true)
            ->get()
            ->sortBy('title');

        $countries = Country::where('active', '=', true)
            ->get()
            ->sortBy('title');

        $categories = ServiceCategory::where('active', '=', true)
            ->whereNull('service_category_id')
            ->get()
            ->sortBy('title');

        $subcategories = ServiceCategory::where('active', '=', true)
            ->whereNotNull('service_category_id')
            ->get()
            ->sortBy('title');

        $services = Service::where('active', '=', true)
            ->whereNotNull('service_category_id')
            ->with('serviceCategory')
            ->get()
            ->sortBy('title');

        $subscription = $provider->getActiveSubscription()?->subscription;

		if (!$subscription) {
			return redirect()->to(urlRouteName('profile'));
		}

        $selectedCategory = old('service_category_id') ?? $provider->service_category_id;

        if($s = old('subcategories')) {
            $selectedSubcategories = ServiceCategory::whereIn('id', explode(',', $s))
                ->where('service_category_id', '=', $selectedCategory)
                ->pluck('id');
        }
        else {
            $selectedSubcategories =  SubscriberServiceCategory::where('subscriber_id', '=', $provider->id)
                ->whereHas('serviceCategory', function($query) use ($selectedCategory) {
                    $query->where('service_category_id', '=', $selectedCategory);
                })
                ->pluck('service_category_id');
        }

        if($s = old('services')) {
            $selectedServices = Service::whereIn('id', $s)
                ->whereIn('service_category_id', $selectedSubcategories)
                ->pluck('id');
        }
        else {
            $selectedServices =  SubscriberService::where('subscriber_id', '=', $provider->id)
                ->whereHas('service', function($query) use ($selectedSubcategories) {
                    $query->whereIn('service_category_id', $selectedSubcategories);
                })
                ->pluck('service_id');
        }

        $servedPostalCodes = old('servedPostalCodes') ?? $provider->postalCodes()->pluck('postal_code');

        return array_merge($params, compact(
            'provider',
            'states',
            'countries',
            'subscription',
            'categories',
            'subcategories',
            'services',
            'selectedSubcategories',
            'selectedServices',
            'servedPostalCodes',
        ));
    }

    public function update(Request $request) {
        $id = $request->input('id');
        $provider = Subscriber::findOrFail($id);
        $subscription = $provider->activeSubscription?->subscription;

        $subscriberData = $request->all([
            'provider_type',
            'company_name',
            'main_description',
            'profile_image',
            'number',
            'street',
            'app',
            'city',
            'state_id',
            'country_id',
            'postal_code',
            'service_category_id',
            'served_state',
            'served_country',
        ]);

        $localizedData = $request->all([
            'fr',
            'en',
        ]);

        $pivotsData = $request->all([
            'subcategories',
            'services',
            'servedPostalCodes',
        ]);

        $validator = Validator::make(array_merge($subscriberData, $pivotsData), [
            'provider_type' => 'required',
            'company_name' => 'required',
            'main_description' => 'required',
            'number' => 'required',
            'street' => 'required',
            'city' => 'required',
            'state_id' => 'required',
            'country_id' => 'required',
            'postal_code' => 'required',
            'service_category_id' => 'required',
            'subcategories' => 'required',
            'services' => 'required',
        ]);

        $validator->setAttributeNames([
            'provider_type' => __('providers.form.provider_type'),
            'company_name' => __('providers.form.company_name'),
            'main_description' => __('providers.form.main_description'),
            'number' => __('providers.form.number'),
            'street' => __('providers.form.street'),
            'city' => __('providers.form.city'),
            'state_id' => __('providers.form.state_id'),
            'country_id' => __('providers.form.country_id'),
            'postal_code' => __('providers.form.postal_code'),
            'service_category_id' => __('providers.form.service_category_id'),
            'subcategories' => __('providers.form.subcategories'),
            'services' => __('providers.form.services'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        if ($subscription->type === 'cities' && !empty($pivotsData['servedPostalCodes']) && count($pivotsData['servedPostalCodes']) > $subscription->max_postal_codes) {
            // checked as a precaution, length is validated client side
            return redirect()->back()
                ->withInput();
        }

        $subscriberData['is_provider'] = true;
		$subscriberData['is_public'] = (boolean)$subscription;

        try {
            $provider->saveElement($subscriberData, true);
        }
        catch (NotReadableException $e) {
            return redirect()->back()
                ->with('error', __('main.error-with-image'))
                ->withInput();
        }

        \DB::table('subscriber_translations')
            ->where('subscriber_id', '=', $provider->id)
            ->where('locale', '=', 'fr')
            ->update([
                'other_service_descriptions' => \Arr::get($localizedData, 'fr.other_service_descriptions')
            ]);

        \DB::table('subscriber_translations')
            ->where('subscriber_id', '=', $provider->id)
            ->where('locale', '=', 'en')
            ->update([
                'other_service_descriptions' => \Arr::get($localizedData, 'en.other_service_descriptions')
            ]);

        SubscriberServiceCategory::where('subscriber_id', '=', $provider->id)
            ->delete();
        SubscriberService::where('subscriber_id', '=', $provider->id)
            ->delete();

        foreach(explode(',', $pivotsData['subcategories']) as $service_category_id) {
            if (empty($service_category_id)) {
                continue;
            }
            SubscriberServiceCategory::create([
               'subscriber_id' => $provider->id,
               'service_category_id' => $service_category_id,
            ]);
        }

        foreach($pivotsData['services'] as $service_id) {
            if (empty($service_id)) {
                continue;
            }
            SubscriberService::create([
                'subscriber_id' => $provider->id,
                'service_id' => $service_id,
            ]);
        }

        if (Arr::get($pivotsData, 'servedPostalCodes')) {
            PostalCode::where('subscriber_id', '=', $provider->id)
                ->delete();
            foreach($pivotsData['servedPostalCodes'] as $postalCode) {
                PostalCode::create([
                    'subscriber_id' => $provider->id,
                    'postal_code' => $postalCode,
                ]);
            }
        }

        return redirect()
            ->back()
            ->with('success', __('providers.updated-success'));
    }

    public function search($params, Request $request) {
        $displayPostalCode = $request->input('displayPostalCode');
        $filterProviderType = $request->input('provider_type');
        $filterSubcategories = $request->input('subcategories');
        $filterCategories = $request->input('categories');
        $filterServices = $request->input('services');
        $filterPostalCode = $request->input('postal_code');

        $resultIds = Subscriber::ProviderSearch(
            $filterProviderType,
            $filterCategories ? collect(explode(',', $filterCategories)) : null,
            $filterSubcategories ? collect(explode(',', $filterSubcategories)) : null,
            $filterServices ? collect(explode(',', $filterServices)): null,
            $filterPostalCode
        )->pluck('id');

        $results = Subscriber::whereIn('id', $resultIds)
            ->with('likedByLoggedInUser')
            ->get();

        $savedSearch = null;
        if ($results->isEmpty()) {
            $savedSearch = SavedSearch::create([
                'obsolete' => true,
                'postal_code' => $filterPostalCode,
                'provider_type' => $filterProviderType,
            ]);
            if ($filterCategories) {
                $categories = $filterCategories;
                if ($filterSubcategories) {
                    $categories .= ',' .$filterSubcategories;
                }
                $savedSearch->saveElement([
                    'serviceCategories' => $categories
                ]);
            }
            if ($filterServices) {
                $savedSearch->saveElement([
                    'services' => $filterServices
                ]);
            }
        }

        return array_merge($params, compact(
            'filterProviderType',
            'filterCategories',
            'filterSubcategories',
            'filterServices',
            'filterPostalCode',
            'results',
            'savedSearch',
            'displayPostalCode',
        ));
    }
}
