<div data-component="postalCodeSearch" data-url="{{ urlRouteName('search-component') }}">
    <div class="content-card">
        <form>
            {{-- Titre + description retirés (Denis 21.06) : le texte « 1er clic » au-dessus
                 explique déjà. Type (résidentiel/B2B) vient du sélecteur de plateforme : champ caché. --}}
            <input type="hidden" name="provider_type" value="{{ $selectedProviderType ?? 'residential' }}">
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
