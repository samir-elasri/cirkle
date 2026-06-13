<div class="provider-single">
    <div style="display: flex; justify-content: space-between; margin-bottom: 16px">
        <div>
        </div>
        @if ($provider->profile_image || setting('default_profile_image'))
            {{ Html::image($provider->profile_image ?? setting('default_profile_image'), '', ['width' => 80]) }}
        @endif
    </div>

    <h3>
        <span style="color:black">{{ __('main.member') }} {{ $provider->formatted_member_number ?? $provider->id }}</span>
        <br>
        {{ $provider->company_name }}
    </h3>

    <div class="content-writable" style="flex-grow:1">
        <p>
            @if ($provider->profile_promotion_active
                || $provider->profile_license_active
                || $provider->profile_diploma_active
                || $provider->profile_image_active
                || $provider->profile_estimation_active
                || $provider->profile_job_offer_active)

                <div>{{ __('main.Ce fournisseur vous offre') }}</div>
                <ul>
                    @if ($provider->profile_promotion_active)
                        <li>{{ setting('promotion_title') }}</li>
                    @endif

                    @if ($provider->profile_license_active)
                        <li>{{ setting('license_title') }}</li>
                    @endif

                    @if ($provider->profile_diploma_active)
                        <li>{{ setting('diploma_title') }}</li>
                    @endif

                    @if ($provider->profile_image_active)
                        <li>{{ setting('image_title') }}</li>
                    @endif

                    @if ($provider->profile_estimation_active)
                        <li>{{ setting('estimation_title') }}</li>
                    @endif

                    @if ($provider->profile_job_offer_active)
                        <li>{{ setting('job_offer_title') }}</li>
                    @endif
                </ul>
            @endif
        </p>
    </div>

    <a style="width:100%;text-align:center" class="call-to-action" href="{{ urlRouteName('provider', ['id' => $provider->id]) }}">
        @lang('providers.view')
    </a>
</div>
