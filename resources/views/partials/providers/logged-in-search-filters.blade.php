<div class="content-card">
    <form method="get" action="{{ urlRouteName('providers-search') }}">
        <div class="form__column ">
            <div class="radio-button-group">
                <input data-ref="typeEl"
                    type="radio"
                    name="provider_type"
                    value="residential"
                    id="provider-type-residential"
                    @if ((!empty($filterProviderType) && $filterProviderType === 'residential') || empty($filterProviderType)) checked @endif
                >
                <label for="provider-type-residential">
                    {{ __('providers.provider-type.residential') }}
                </label>

                <input data-ref="typeEl"
                    type="radio"
                    name="provider_type"
                    value="business"
                    id="provider-type-business"
                    @if (!empty($filterProviderType) && $filterProviderType === 'business') checked @endif
                >
                <label for="provider-type-business">
                    {{ __('providers.provider-type.business') }}
                </label>
            </div>
        </div>
        <p>{{ __('providers.search.title') }}</p>
        <div class="form__container">
            <div class="form__column ">
                <div class="ui selection dropdown fluid search"
                     data-component="dropdown">
                    <input data-ref="dropdownEl"
                           type="hidden"
                           value="{{ $filterCategories ?? '' }}"
                           name="categories"
                           id="categories">
                    <i class="dropdown icon"></i>
                    <div class="default text">
                        @lang('providers.search.category')
                    </div>
                    <div class="menu">
                        @foreach ($categories as $category)
                            <div class="item"
                                 data-value="{{ $category->id }}">
                                {{ $category->title }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="form__column ">
                <div data-component="filteredDropdown"
                     data-parent-input-id="categories"
                     data-hide-if-parent-empty="true"
                     data-no-cache="false">
                    <div class="ui selection dropdown fluid multiple search"
                         data-component="dropdown">
                        <input data-ref="dropdownEl"
                               type="hidden"
                               value="{{ $filterSubcategories ?? '' }}"
                               name="subcategories"
                               id="subcategories">
                        <i class="dropdown icon"></i>
                        <div class="default text">
                            @lang('providers.search.subcategories')
                        </div>
                        <div class="menu">
                            @foreach ($subcategories as $category)
                                <div class="item hide"
                                     data-value="{{ $category->id }}"
                                     data-parent="{{ $category->service_category_id }}">
                                    {{ $category->title }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="form__column ">
                <div data-component="filteredDropdown"
                     data-parent-input-id="subcategories"
                     data-hide-if-parent-empty="true"
                     data-no-cache="true">
                    <div class="ui selection dropdown fluid multiple search"
                         data-component="dropdown" data-labels="false" data-count="{{trans('main.selected')}}">
                        <input data-ref="dropdownEl"
                               type="hidden"
                               value="{{ $filterServices ?? '' }}"
                               name="services"
                               id="services">
                        <i class="dropdown icon"></i>
                        <div class="default text">
                            @lang('providers.search.services')
                        </div>
                        <div class="menu">
                            @foreach ($services as $service)
                                <div class="item hide"
                                     data-parent="{{ $service->service_category_id }}"
                                     data-value="{{ $service->id }}">
                                    {{ $service->title }}
                                    -
                                    {{ $service->serviceCategory?->title }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card__footer">
                <button data-ref="submitBtn" type="submit" class="call-to-action">
                    {{ __('providers.search.filter-submit') }}
                </button>
            </div>
        </div>
    </form>
</div>
