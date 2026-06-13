@php
    // Professions présentes (id) + regroupées par catégorie pour le catalogue récursif.
    $presentIds = $presentProfessions->pluck('id')->all();
    $professionsByCategory = $allProfessions->groupBy('service_category_id');
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
</style>

<div class="search-result" data-component="searchResult">
    <script type="application/json">{!! json_encode([
        'allProfessions' => $allProfessions->map->only(['id', 'service_category_id', 'title', 'url']),
        'presentProfessions' => $presentProfessions->pluck('id')
    ]) !!}</script>

    {{-- Filtre rapide par catégorie (JS existant) --}}
    <div class="search-result__filters">
        <div class="content-card">
            <form>
                <div class="ui selection dropdown search" data-component="dropdown" >
                    {{ Form::hidden('service_category_id', null, ['data-ref' => "categoryFilter"]) }}
                    <i class="dropdown icon"></i>
                    <div class="default text">{{ __('main.category') }}</div>
                    <div class="menu">
                        @foreach ($allCategories as $category)
                            @if (!$category->title) @continue @endif
                            <div class="item" data-value="{{ $category->id }}">{{ $category->title }}</div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="search-result__section" data-ref="filteredProfessionsContainer"></div>

    @include('core.partials.spacing', ['spacing' => 40])

    {{-- Catalogue récursif TOUJOURS visible : catégorie (gauche) → professions (droite).
         En vert/cliquable = profession avec fournisseurs dans le code postal (+ nombre de membres). --}}
    <h3>{{ __('main.catalogueTitle') }}</h3>
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
</div>
