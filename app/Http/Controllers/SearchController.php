<?php

namespace App\Http\Controllers;

use App\Services\SearchDataService;
use App\Services\SearchService;
use Illuminate\Http\Request;
use View;

class SearchController extends Controller
{
    public function __construct(
        private SearchDataService $searchDataService,
        private SearchService $searchService,
    ) {
        parent::__construct();
    }

    public function search(Request $request)
    {
        $postalCode = $request->input('postal_code');
        $providerType = $request->input('provider_type');

        $this->searchDataService->storePostalCode($postalCode);
        $this->searchDataService->storeProviderType($providerType);

        return View::make('partials.search.search', [
            'allCategories'         => $this->searchService->getAllCategories(),
            'allProfessions'        => $this->searchService->getAllProfessions(),
            'presentProfessions'    => $this->searchService->getProfessionsInPostalCode($postalCode, $providerType),
            'nonPresentProfessions' => $this->searchService->getProfessionsNotInPostalCode($postalCode, $providerType),
            'presentCategories'     => $this->searchService->getCategoriesInPostalCode($postalCode, $providerType),
            'nonPresentCategories'  => $this->searchService->getCategoriesNotInPostalCode($postalCode, $providerType),
        ]);
    }

    public function category($params, Request $request, $id)
    {
        $postalCode = $this->searchDataService->getStoredPostalCode();
        $providerType = $this->searchDataService->getStoredProviderType();

        if (!$this->searchDataService->hasStoredPostalCode() || !$this->searchDataService->hasStoredPostalCode()) {
            return redirect()->to(urlRouteName('home'));
        }

        $serviceCategory = $this->searchService->getCategory($id);
        $presentProfessions = $this->searchService->getProfessionsInPostalCode($postalCode, $providerType);
        $nonPresentProfessions = $this->searchService->getProfessionsNotInPostalCode($postalCode, $providerType);


        return array_merge($params, [
            'title' => $serviceCategory->title,
            'serviceCategory' => $serviceCategory,
            'presentProfessions' => $presentProfessions,
            'nonPresentProfessions' => $nonPresentProfessions,
        ]);
    }

    public function profession($params, Request $request, $id)
    {
        if (!$this->searchDataService->hasStoredPostalCode() || !$this->searchDataService->hasStoredPostalCode()) {
            return redirect()->to(urlRouteName('home'));
        }

        $postalCode = $this->searchDataService->getStoredPostalCode();
        $providerType = $this->searchDataService->getStoredProviderType();

        $profession = $this->searchService->getCategory($id);
        $subscribers = $this->searchService->getSubscribersWithProfessionInPostalCode($postalCode, $providerType, $profession->id);

        return array_merge($params, [
            'title' => $profession->title,
            'subscribers' => $subscribers,
        ]);
    }
}
