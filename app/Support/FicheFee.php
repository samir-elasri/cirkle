<?php

namespace App\Support;

/**
 * Frais UNIQUE de la fiche de compétence (Denis) : un montant fixe selon la PLATEFORME
 * du fournisseur (provider_type), pas selon la profession.
 *   - résidentiel : 75 $
 *   - B2B (business) : 100 $
 *
 * Configurable via les réglages `fiche_fee_residential` / `fiche_fee_business`
 * (repli sur 75 / 100 si absents). Source unique pour l'affichage (porte d'acceptation)
 * et la facturation (panier).
 */
class FicheFee
{
    public static function residential(): float
    {
        return (float) (setting('fiche_fee_residential') ?: 75);
    }

    public static function business(): float
    {
        return (float) (setting('fiche_fee_business') ?: 100);
    }

    /** Montant fixe selon la plateforme choisie. */
    public static function for(?string $providerType): float
    {
        return $providerType === 'business' ? self::business() : self::residential();
    }
}
