{{ $blocs }}

<section>
    <div class="optimal-content-width">
        <div class="provider-search">
            @if (logged_in())
                <div class="tile-row">
                    @include ('partials.providers.logged-in-search-filters')
                </div>
            @endif

            <div class="provider-search__results">
                @if ($results->count())
                    <p>{{ trans_choice('providers.search.results', $results->count(), ['count' => $results->count()]) }}</p>

                    @foreach ($results as $provider)
                        @include ('partials.providers.single')
                    @endforeach
                @else
                    <p>{{ __('providers.search.no-result') }}</p>
                    @if(logged_in())
                        <div>
                            <button type="button" class="call-to-action" data-component="modal" data-modal-template="save-search-modal">
                                <script type="application/json">{!! json_encode([
                                    'options' => [
                                        'showCloseButton' => true,
                                        'showConfirmButton' => false,
                                        'title' => __('providers.search.save.title'),
                                    ]
                                ], JSON_THROW_ON_ERROR) !!}</script>
                                {{ __('providers.search.save.cta') }}
                            </button>
                        </div>

                        <template id="save-search-modal">
                            <p>{{ __('providers.search.save.text') }}</p>
                            <a href="{{ urlRouteName('attach-saved-search', ['id' => $savedSearch->id ]) }}" class="call-to-action">
                                {{ __('providers.search.save.submit') }}
                            </a>
                        </template>
                    @endif
                @endif
            </div>
        </div>
    </div>
</section>
