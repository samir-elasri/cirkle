{{--
    Sélecteur des 4 plateformes : Residential English / Résidentiel Français / B2B English / B2B Français.
    Plateforme = locale (URL /fr ou /en) × type de fournisseur (residential|business, en session
    via SearchDataService — voir SearchController@home). La plateforme choisie est mise en
    évidence EN JAUNE (spec : CIRKLE PAGE ACCUEIL 010626.xlsx).
    Les libellés sont volontairement chacun dans leur propre langue, peu importe la locale courante.
--}}
@php
    $currentLocale = app()->getLocale();
    $selectedType = $selectedProviderType ?? null;
    // Plateformes FRANÇAISES « À VENIR » tant que les professions anglaises ne sont pas finies
    // (Denis 30.06). Pour les ACTIVER plus tard : mettre $frenchComingSoon = false.
    $frenchComingSoon = true;
    $platforms = [
        ['locale' => 'en', 'type' => 'residential', 'label' => __('main.platformResidentialEn')],
        ['locale' => 'fr', 'type' => 'residential', 'label' => __('main.platformResidentialFr')],
        ['locale' => 'en', 'type' => 'business',    'label' => __('main.platformBusinessEn')],
        ['locale' => 'fr', 'type' => 'business',    'label' => __('main.platformBusinessFr')],
    ];
@endphp

{{-- Styles inline temporaires : la rebuild SCSS est gelée (voir docs/gap-map.md) --}}
<style>
    .platform-selector {
        margin-bottom: 1.5em;
    }
    .platform-selector__title {
        text-align: center;
        font-weight: 700;
        margin-bottom: .75em;
    }
    .platform-selector__tiles {
        display: flex;
        flex-wrap: wrap;
        gap: .75em;
        justify-content: center;
    }
    .platform-selector__tile {
        flex: 1 1 200px;
        max-width: 280px;
        padding: 1em 1.25em;
        border: 2px solid currentColor;
        border-radius: 6px;
        text-align: center;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
        text-decoration: none;
        color: inherit;
    }
    .platform-selector__tile:hover {
        background: #fff3c4;
        text-decoration: none;
    }
    .platform-selector__tile.is-active {
        background: #ffd54d; /* jaune : plateforme choisie en évidence */
        border-color: #e6b800;
    }
    /* Plateforme « À VENIR » (français, pour le moment) : rouge, non cliquable. */
    .platform-selector__tile.is-soon {
        color: #c0392b; border-color: #e7b3ad; background: #fdf0ee;
        cursor: not-allowed; position: relative;
    }
    .platform-selector__tile.is-soon:hover { background: #fdf0ee; }
    .platform-selector__soon-badge {
        display: block; font-size: .72em; font-weight: 800; letter-spacing: .08em; color: #c0392b; margin-top: 3px;
    }
</style>

<div class="platform-selector">
    <p class="platform-selector__title">{{ __('main.platformSelectorTitle') }}</p>
    <div class="platform-selector__tiles">
        @foreach ($platforms as $platform)
            @if ($frenchComingSoon && $platform['locale'] === 'fr')
                {{-- Plateforme française : À VENIR (non cliquable) --}}
                <span class="platform-selector__tile is-soon" title="{{ __('main.platformComingSoon') }}">
                    {{ $platform['label'] }}
                    <span class="platform-selector__soon-badge">{{ __('main.platformComingSoon') }}</span>
                </span>
            @else
                <a class="platform-selector__tile {{ $currentLocale === $platform['locale'] && $selectedType === $platform['type'] ? 'is-active' : '' }}"
                   href="{{ url($platform['locale']) }}?platform={{ $platform['type'] }}">
                    {{ $platform['label'] }}
                </a>
            @endif
        @endforeach
    </div>
</div>
