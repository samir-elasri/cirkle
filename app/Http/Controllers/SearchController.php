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

    /**
     * Page d'accueil : gère le sélecteur de plateforme (locale × type de fournisseur).
     * La locale vient de l'URL (/fr, /en); le type choisi est conservé en session
     * via SearchDataService — le même mécanisme que la recherche par code postal.
     */
    public function home($params, Request $request)
    {
        $platform = $request->query('platform');

        if (in_array($platform, ['residential', 'business'], true)) {
            $this->searchDataService->storeProviderType($platform);
        }

        $params['selectedProviderType'] = $this->searchDataService->getStoredProviderType();

        return $params;
    }

    public function search(Request $request)
    {
        $postalCode = $request->input('postal_code');
        // Les boutons radio résidentiel/B2B ont été retirés (le type vient du sélecteur de
        // plateforme, en session). On retombe donc sur le type stocké, sinon « residential ».
        $providerType = $request->input('provider_type')
            ?: ($this->searchDataService->getStoredProviderType() ?: 'residential');

        $this->searchDataService->storePostalCode($postalCode);
        $this->searchDataService->storeProviderType($providerType);

        return View::make('partials.search.search', [
            'allCategories'         => $this->searchService->getAllCategories(),
            // Catalogue filtré par plateforme : évite le doublon RE/B2BE d'une même profession.
            'allProfessions'        => $this->searchService->getAllProfessionsForType($providerType),
            'presentProfessions'    => $this->searchService->getProfessionsInPostalCode($postalCode, $providerType),
            'nonPresentProfessions' => $this->searchService->getProfessionsNotInPostalCode($postalCode, $providerType),
            'presentCategories'     => $this->searchService->getCategoriesInPostalCode($postalCode, $providerType),
            'nonPresentCategories'  => $this->searchService->getCategoriesNotInPostalCode($postalCode, $providerType),
            'professionCounts'      => $this->searchService->getProfessionSupplierCountsInPostalCode($postalCode, $providerType),
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
        $profession = $this->searchService->getCategory($id);

        // Avec un code postal en session : fournisseurs de la région. Sinon (ex. clic direct
        // sur une profession du catalogue de l'accueil) : tous les fournisseurs publics de la
        // profession — on n'efface plus l'écran avec une redirection vers l'accueil (Denis 30.06).
        if ($this->searchDataService->hasStoredPostalCode()) {
            $subscribers = $this->searchService->getSubscribersWithProfessionInPostalCode(
                $this->searchDataService->getStoredPostalCode(),
                $this->searchDataService->getStoredProviderType(),
                $profession->id
            );
        } else {
            $subscribers = $this->searchService->getAllSubscribersWithProfession($profession->id);
        }

        return array_merge($params, [
            'title' => $profession->title,
            'profession' => $profession,
            'subscribers' => $subscribers,
        ]);
    }
}
