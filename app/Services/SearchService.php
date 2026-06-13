<?php

namespace App\Services;

use App\Models\PostalCode;
use App\Models\Core\Subscriber;
use Illuminate\Support\Collection;
use App\Models\ServiceCategory;

class SearchService
{
    private array $professionIdsCache = [];
    private array $categoryIdsCache = [];

    public function getCategory(int $id): ServiceCategory
    {
        return ServiceCategory::findOrFail($id);
    }

    public function getAllCategories(): Collection
    {
        return ServiceCategory::whereNull('service_category_id')->get();
    }

    public function getAllProfessions(): Collection
    {
        return ServiceCategory::whereNotNull('service_category_id')->get();
    }

    public function getSubscribersInPostalCode(string $postalCode, string $providerType): Collection
    {
        $subscriberIds = $this->getSubscriberIdsInPostalCode($postalCode);
        return Subscriber::whereIn('id', $subscriberIds)
            ->where('active', true)
            ->where('is_public', true)
            ->where('registration_completed', true)
            ->whereIn('provider_type', $this->getProviderTypesForSearch($providerType))
            ->get();
    }

    public function getSubscribersWithProfessionInPostalCode(string $postalCode, string $providerType, $professionId): Collection
    {
        $subscriberIds = $this->getSubscriberIdsInPostalCode($postalCode);
        return Subscriber::whereIn('id', $subscriberIds)
            ->where('active', true)
            ->where('is_public', true)
            ->where('registration_completed', true)
            ->whereIn('provider_type', $this->getProviderTypesForSearch($providerType))
            ->where('service_category_id', $professionId)
            ->get();
    }


    public function getProfessionsInPostalCode(string $postalCode, string $providerType): Collection
    {
        $professionIds = $this->getProfessionIdsInPostalCode($postalCode, $providerType);
        return ServiceCategory::whereIn('id', $professionIds)
            ->whereNotNull('service_category_id')
            ->get();
    }

    public function getProfessionsNotInPostalCode(string $postalCode, string $providerType): Collection
    {
        $professionIds = $this->getProfessionIdsInPostalCode($postalCode, $providerType);
        return ServiceCategory::whereNotIn('id', $professionIds)
            ->whereNotNull('service_category_id')
            ->get();
    }

    public function getCategoriesInPostalCode(string $postalCode, string $providerType): Collection
    {
        $categoryIds = $this->getCategoryIdsInPostalCode($postalCode, $providerType);
        return ServiceCategory::whereIn('id', $categoryIds)
            ->whereNull('service_category_id')
            ->get();
    }

    public function getCategoriesNotInPostalCode(string $postalCode, string $providerType): Collection
    {
        $categoryIds = $this->getCategoryIdsInPostalCode($postalCode, $providerType);
        return ServiceCategory::whereNotIn('id', $categoryIds)
            ->whereNull('service_category_id')
            ->get();
    }

    private function getSubscriberIdsInPostalCode(string $postalCode): Collection
    {
        return PostalCode::where('postal_code', $postalCode)
            ->distinct('subscriber_id')
            ->pluck('subscriber_id');
    }

    /**
     * Nombre de fournisseurs visibles par profession dans un code postal
     * (pour afficher le « nombre de membres » à côté des professions vertes).
     *
     * @return array<int,int> [profession_id => count]
     */
    public function getProfessionSupplierCountsInPostalCode(string $postalCode, $providerType): array
    {
        $subscriberIds = $this->getSubscriberIdsInPostalCode($postalCode);

        return Subscriber::whereIn('id', $subscriberIds)
            ->where('active', true)
            ->where('is_public', true)
            ->where('registration_completed', true)
            ->whereIn('provider_type', $this->getProviderTypesForSearch($providerType))
            ->whereNotNull('service_category_id')
            ->selectRaw('service_category_id, COUNT(*) as total')
            ->groupBy('service_category_id')
            ->pluck('total', 'service_category_id')
            ->toArray();
    }



    private function getProfessionIdsInPostalCode(string $postalCode, $providerType): array
    {
        $cacheKey = "{$postalCode}_{$providerType}";
        
        if (isset($this->professionIdsCache[$cacheKey])) {
            return $this->professionIdsCache[$cacheKey];
        }

        $subscriberIds = $this->getSubscriberIdsInPostalCode($postalCode);
        
        $query = Subscriber::whereIn('id', $subscriberIds)
            ->whereIn('provider_type', $this->getProviderTypesForSearch($providerType));
        
        $professionIds = $query->distinct('service_category_id')
            ->pluck('service_category_id')
            ->toArray();

        $this->professionIdsCache[$cacheKey] = $professionIds;
        
        return $professionIds;
    }

    private function getProviderTypesForSearch($providerType) {
        return match($providerType) {
            'residential' => ['both', 'residential'],
            'business' => ['both', 'business'],
            default => ['both', 'residential', 'business'],
        };
    }

    private function getCategoryIdsInPostalCode(string $postalCode, $providerType): array
    {
        $cacheKey = "{$postalCode}_{$providerType}";
        
        if (isset($this->categoryIdsCache[$cacheKey])) {
            return $this->categoryIdsCache[$cacheKey];
        }

        $professionIds = $this->getProfessionIdsInPostalCode($postalCode, $providerType);
        
        $categoryIds = ServiceCategory::whereIn('id', $professionIds)
            ->whereNotNull('service_category_id')
            ->distinct('service_category_id')
            ->pluck('service_category_id')
            ->filter()
            ->toArray();

        $this->categoryIdsCache[$cacheKey] = $categoryIds;
        
        return $categoryIds;
    }
}
