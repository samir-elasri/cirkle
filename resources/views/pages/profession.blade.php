{!! $blocs !!}

<section>
    <div class="optimal-content-width">
        @isset($profession)
            <div style="display:flex;align-items:center;gap:.6em;margin-bottom:1em">
                <h2 style="margin:0">{{ $profession->title }}</h2>
                @if (logged_in())
                    {{-- Favori « profession » (feature #11) --}}
                    @include('partials.providers.like-profession', ['profession' => $profession])
                @endif
            </div>
        @endisset

        @include('partials.help-note', ['text' => __('main.help.profession')])

        {{-- Liste VERTICALE des fournisseurs (Denis 01.07 : « liste verticale des fourn
             exactement comme Mbiance l'a suggéré dans sa vidéo no 6 ») : un bloc pleine
             largeur par fournisseur, empilés — au lieu des cartes de 300 px côte à côte.
             Styles inline : rebuild SCSS gelée. --}}
        <style>
            .provider-search__results { flex-direction: column; }
            .provider-search__results .provider-single { width: 100%; }
        </style>
        <div class="provider-search">
            <div class="provider-search__results">
                @forelse ($subscribers as $provider)
                    @include ('partials.providers.single')
                @empty
                    <div class="ck-help" style="text-align:center;padding:18px">
                        {{ app()->getLocale() === 'en'
                            ? 'No supplier is registered for this profession yet — please check back soon.'
                            : "Aucun fournisseur n'est encore inscrit pour cette profession — revenez bientôt." }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
