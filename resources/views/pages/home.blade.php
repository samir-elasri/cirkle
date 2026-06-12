<section>
    <div class="optimal-content-width">
        @include ('partials.platform-selector')

        <div class="tile-row">
            @include ('partials.providers.public-search-filters')
        </div>

        <div class="tile-row">
            <div class="content-card content-card--half-width content-card--with-icon">
                <div class="content-card__header">
                    <div><img src="/dist/img/client_advantage.svg" alt=""></div>
                    <h3>
                        {!! setting('home_client_advantage_title1') !!}
                        <br>
                        <span class="color-black">{!! setting('home_client_advantage_title2') !!}</span>
                    </h3>
                </div>

                {!! setting('home_client_advantage_content') !!}

                <div class="content-card__footer">
                    <a href="{{ setting('home_client_link1_url') }}" class="call-to-action cta-alt">{{ setting('home_client_link1_label') }}</a>
                    <a href="{{ setting('home_client_link2_url') }}" class="call-to-action">{{ setting('home_client_link2_label') }}</a>
                </div>
            </div>

            <div class="content-card content-card--half-width content-card--with-icon">
                <div class="content-card__header">
                    <div><img src="/dist/img/supplier_advantage.svg" alt=""></div>
                    <h3>
                        {!! setting('home_provider_advantage_title1') !!}
                        <br>
                        <span class="color-black">{!! setting('home_provider_advantage_title2') !!}</span>
                    </h3>
                </div>

                {!! setting('home_provider_advantage_content') !!}

                <div class="content-card__footer">
                    <a href="{{ setting('home_provider_link1_url') }}" class="call-to-action cta-alt">{{ setting('home_provider_link1_label') }}</a>
                    <a href="{{ setting('home_provider_link2_url') }}" class="call-to-action">{{ setting('home_provider_link2_label') }}</a>
                </div>
            </div>
        </div>
    </div>
</section>

{!! $blocs !!}
