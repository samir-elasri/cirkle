<div data-component="postalCodeSearch" data-url="{{ urlRouteName('search-component') }}">
    <div class="content-card">
        <form>
            <p style="font-weight:700;margin:0 0 .2em">{{ __('providers.search.title-public') }}</p>
            {{-- Type (résidentiel/B2B) vient du sélecteur de plateforme : champ caché, plus de boutons radio (Denis 11:18) --}}
            <input type="hidden" name="provider_type" value="{{ $selectedProviderType ?? 'residential' }}">
            <p style="margin:0 0 .6em">
                @if(app()->getLocale() === 'en')
                    Once you enter your postal code, Cirkle will download <span class="ck-home__green">the list of professions</span> for that region.
                @else
                    Après avoir inscrit le code postal, Cirkle téléchargera <span class="ck-home__green">la liste des professions</span> de cette région.
                @endif
            </p>
            <div class="form__container">
                <div class="form__row">
                    <div class="form__column">
                        <div class="postal-code__icon">
                            <input id="postal_code" placeholder="{{trans('providers.search.postal_code')}}" type="text">
                        </div>
                    </div>
                    <div class="form__column">
                        <button type="submit" class="call-to-action">
                            {{ __('providers.search.filter-submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="postalCodeSearch__result"></div>
</div>
