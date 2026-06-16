<?php

/**
 * Forfait « site web du fournisseur » (MASTER 2350, section SITE WEB).
 * Le fournisseur choisit UN SEUL couple palier × durée. Prix fixes (cahier de charges).
 *
 *   palier => [ durée (mois) => prix ($) ]
 *
 * Source : « FRAIS SITE WEB DU FOURN 100 $ / 150 $ ». Modifier ici si Denis ajuste
 * les prix (puis `php artisan config:clear` — déjà fait au déploiement).
 */
return [
    'tiers' => [
        100 => [1 => 100, 6 => 500, 12 => 1000],
        150 => [1 => 150, 6 => 800, 12 => 1600],
    ],
];
