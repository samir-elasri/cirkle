@php
    // Professions présentes (id) + regroupées par catégorie pour le catalogue récursif.
    $presentIds = $presentProfessions->pluck('id')->all();
    $professionsByCategory = $allProfessions->groupBy('service_category_id');
    $loc = app()->getLocale();
    $totalProfs = $allProfessions->filter(fn ($p) => !empty($p->title))->count();
    $presentCount = count($presentIds);
@endphp

{{-- Catalogue de recherche par code postal (feature #2).
     Styles inline temporaires : la rebuild SCSS est gelée (voir docs/gap-map.md) --}}
<style>
    .catalogue__legend { color: #69716b; font-size: .9em; margin: .25em 0 1.25em; }
    .catalogue__row {
        display: grid;
        grid-template-columns: minmax(150px, 1fr) 3fr;
        gap: 1em;
        padding: .75em 0;
        border-bottom: 1px solid #eef1ec;
        align-items: start;
    }
    .catalogue__cat { font-weight: 700; color: #14181f; }
    .catalogue__cat--available { color: #157a47; }
    .catalogue__profs {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: .25em .9em;
    }
    .catalogue__prof { color: #14181f; }                 /* noir, non cliquable */
    a.catalogue__prof--available {                         /* vert, cliquable */
        color: #1b9c5a;
        font-weight: 600;
        text-decoration: none;
    }
    a.catalogue__prof--available:hover { text-decoration: underline; }
    .catalogue__count {
        color: #157a47;
        font-size: .82em;
        font-weight: 600;
        margin-left: .25em;
    }
    .catalogue__empty {
        background: #fff8e1; border: 1px solid #ffe082; border-left: 4px solid #ffc107;
        border-radius: 10px; padding: .9em 1.1em; margin: .5em 0 1em;
        color: #5a4d00; line-height: 1.5; font-size: .95em;
    }
</style>

<div class="search-result" data-component="searchResult">
    <script type="application/json">{!! json_encode([
        'allProfessions' => $allProfessions->map->only(['id', 'service_category_id', 'title', 'url']),
        'presentProfessions' => $presentProfessions->pluck('id')
    ]) !!}</script>

    {{-- Boîte « Catégories » + titre « Catalogue des services » retirés (Denis 11:18) :
         Cirkle affiche déjà toutes les professions, en vert celles disponibles dans la
         région — le client clique directement dessus. --}}

    @if ($totalProfs === 0)
        {{-- Aucune fiche encore importée --}}
        <div class="catalogue__empty">
            {{ $loc === 'en'
                ? 'No professions are available yet — recruitment is underway. Please check back soon. 🙂'
                : 'Aucune profession n’est encore disponible — le recrutement est en cours. Revenez bientôt. 🙂' }}
        </div>
    @else
        @if ($presentCount === 0)
            {{-- Des professions existent, mais aucun fournisseur dans ce code postal --}}
            <div class="catalogue__empty">
                {{ $loc === 'en'
                    ? 'No supplier in this postal code yet — try a nearby postal code. The full list of professions on Cirkle is shown below.'
                    : 'Aucun fournisseur dans ce code postal pour le moment — essayez un code postal avoisinant. Voici la liste complète des professions sur Cirkle.' }}
            </div>
        @endif

        <div class="catalogue__legend">{{ __('main.catalogueLegend') }}</div>

        <div class="catalogue">
        @foreach ($allCategories as $category)
            @if (!$category->title) @continue @endif
            @php($professions = ($professionsByCategory[$category->id] ?? collect())->filter(fn ($p) => !empty($p->title)))
            @if ($professions->isEmpty()) @continue @endif
            @php($categoryHasAvailable = $professions->contains(fn ($p) => in_array($p->id, $presentIds)))

            <div class="catalogue__row">
                <div class="catalogue__cat {{ $categoryHasAvailable ? 'catalogue__cat--available' : '' }}">
                    {{ $category->title }}
                </div>
                <div class="catalogue__profs">
                    @foreach ($professions as $profession)
                        @if (in_array($profession->id, $presentIds))
                            <a class="catalogue__prof--available" href="{{ $profession->url }}">
                                {{ $profession->title }}@if(!empty($professionCounts[$profession->id]))<span class="catalogue__count" title="{{ $professionCounts[$profession->id] }} {{ __('main.membersShort') }}">({{ $professionCounts[$profession->id] }})</span>@endif
                            </a>
                        @else
                            <span class="catalogue__prof">{{ $profession->title }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
        </div>
    @endif
</div>
