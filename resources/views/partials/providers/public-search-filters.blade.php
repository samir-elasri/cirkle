<div data-component="postalCodeSearch" data-url="{{ urlRouteName('search-component') }}">
    <div class="content-card">
        <form>
            {{-- Titre + description retirés (Denis 21.06) : le texte « 1er clic » au-dessus explique déjà.
                 IMPORTANT : le composant JS gelé « postalCodeSearch » lit #provider-type-residential.checked
                 et #provider-type-business.checked — on garde donc les 2 radios mais CACHÉS (le type vient
                 du sélecteur de plateforme). Les retirer faisait planter le clic « Rechercher » (null.checked). --}}
            <input type="radio" name="provider_type" id="provider-type-residential" value="residential" @checked(($selectedProviderType ?? 'residential') !== 'business') hidden>
            <input type="radio" name="provider_type" id="provider-type-business" value="business" @checked(($selectedProviderType ?? null) === 'business') hidden>
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
