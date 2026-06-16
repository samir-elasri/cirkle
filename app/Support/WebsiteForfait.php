<?php

namespace App\Support;

/**
 * Forfait « site web du fournisseur » : helpers de lecture des paliers/durées/prix
 * (config/website_forfait.php). Le choix du fournisseur est encodé « palier-durée »
 * (ex. « 150-6 » = palier 150 $, 6 mois).
 */
class WebsiteForfait
{
    /** @return array<int, array<int, float|int>> palier => [durée => prix] */
    public static function tiers(): array
    {
        return (array) config('website_forfait.tiers', []);
    }

    public static function isValid(?string $choice): bool
    {
        return self::price($choice) !== null;
    }

    /** Prix ($) du choix « palier-durée », ou null si invalide. */
    public static function price(?string $choice): ?float
    {
        [$tier, $months] = self::parse($choice);
        if ($tier === null) {
            return null;
        }
        $price = config("website_forfait.tiers.{$tier}.{$months}");

        return $price !== null ? (float) $price : null;
    }

    /** Durée (mois) du choix, ou null. */
    public static function months(?string $choice): ?int
    {
        return self::parse($choice)[1];
    }

    /** @return array{0:?int,1:?int} [palier, durée] */
    private static function parse(?string $choice): array
    {
        if (!$choice || !str_contains($choice, '-')) {
            return [null, null];
        }
        [$tier, $months] = explode('-', $choice, 2);
        if (!ctype_digit($tier) || !ctype_digit($months)) {
            return [null, null];
        }

        return [(int) $tier, (int) $months];
    }
}
