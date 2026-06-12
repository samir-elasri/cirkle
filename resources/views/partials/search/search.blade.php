<div class="search-result" data-component="searchResult">
    <script type="application/json">{!! json_encode([
        'allProfessions' => $allProfessions->map->only(['id', 'service_category_id', 'title', 'url']),
        'presentProfessions' => $presentProfessions->pluck('id')
    ]) !!}</script>
    <div class="search-result__filters">
        <div class="content-card">
            <form>
                <div class="ui selection dropdown search" data-component="dropdown" >
                    {{ Form::hidden('service_category_id', null, ['data-ref' => "categoryFilter"]) }}
                    <i class="dropdown icon"></i>
                    <div class="default text">Catégorie</div>
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

    @include('core.partials.spacing', ['spacing' => 50])

    <h3>{{ __('main.Les catégories Cirkle') }}</h3>
    <div class="search-result__section">
        @foreach($presentCategories as $element)
            @if (!$element->title) @continue @endif
            <a href="{{ $element->url }}">{{ $element->title }}</a>
        @endforeach
        @foreach($nonPresentCategories as $element)
            @if (!$element->title) @continue @endif
            <span>{{ $element->title }}</span>
        @endforeach
    </div>

    @if (!$presentProfessions->isEmpty())
        @include('core.partials.spacing', ['spacing' => 50])
        <h3>{{ __('main.Les professions avec fournisseur dans votre code postal') }}</h3>
        <div class="search-result__section">
            @foreach($presentProfessions as $element)
                @if (!$element->title) @continue @endif
                <a href="{{ $element->url }}">{{ $element->title }}</a>
            @endforeach
        </div>
    @endif

    @if (!$nonPresentProfessions->isEmpty())
        @include('core.partials.spacing', ['spacing' => 50])
        <h3>{{ __('main.Les professions sans fournisseur dans votre code postal') }}</h3>
        <div class="search-result__section">
            @foreach($nonPresentProfessions as $element)
                @if (!$element->title) @continue @endif
                <span>{{ $element->title }}</span>
            @endforeach
        </div>
    @endif
</div>
