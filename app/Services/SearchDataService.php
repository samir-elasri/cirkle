<?php

namespace App\Services;

class SearchDataService
{
    public function storePostalCode(string $postalCode): void
    {
        session()->put('SearchDataService.postalCode', $postalCode);
    }

    public function getStoredPostalCode(): ?string
    {
        return session()->get('SearchDataService.postalCode');
    }

    public function hasStoredPostalCode(): bool
    {
        return !empty($this->getStoredPostalCode());
    }

    public function storeProviderType(string $providerType): void
    {
        session()->put('SearchDataService.providerType', $providerType);
    }

    public function getStoredProviderType(): ?string
    {
        return session()->get('SearchDataService.providerType');
    }

    public function hasStoredProviderType(): bool
    {
        return !empty($this->getStoredProviderType());
    }

}
