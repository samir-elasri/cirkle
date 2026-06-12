<div data-component="postalCodeSearch" data-url="{{ urlRouteName('search-component') }}">
    <div class="content-card">
        <form>
            <p>{{ __('providers.search.title-public') }}</p>
            <p>{{ __('providers.search.description-public') }}</p>
            <div class="form__container">
                <div class="radio-button-group">
                    <input type="radio" name="provider_type" value="residential" id="provider-type-residential" checked>
                    <label for="provider-type-residential">{{ __('providers.provider-type.residential') }}</label>

                    <input type="radio" name="provider_type" value="business" id="provider-type-business">
                    <label for="provider-type-business">{{ __('providers.provider-type.business') }}</label>
                </div>
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
