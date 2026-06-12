@if (!empty($serviceCategory->services_intro_text))
    <div class="form__column">
        {!! $serviceCategory->services_intro_text !!}
    </div>
@endif

<div class="form__column">
    <div class="registration-title">{{ __('auth.register.services') }}</div>
    <div class="form__row--error">
        @foreach($errors->get('services', '<small style="color: red">:message</small>') as $error)
            {!! $error !!}
        @endforeach
    </div>
    @foreach ($serviceCategory->services as $service)
        @if (!$service->title) @continue @endif
        <div class="form__column">
            <div class="form__row" @if (!empty($service->input_label)) data-component="step2ConditionnalCheckboxInput" @endif>
                <sl-checkbox
                    name="services[]"
                    value="{{ $service->id }}"
                    @if (in_array($service->id, old('services') ?? $existingServices ?? (session('registerFormData.services') ?? []))) checked @endif
                >
                    {{ $service->title }}
                </sl-checkbox>
                @if (!empty($service->input_label))
                    <input type="text" 
                           name="service_input[{{ $service->id }}]" 
                           value="{{ old('service_input.' . $service->id) ?? ($existingServiceInputs[$service->id] ?? (session('registerFormData.service_input.' . $service->id) ?? '')) }}"
                           placeholder="{{ $service->input_label }}"
                    >
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="form__column">
    <div class="form__row--error">
        @foreach($errors->get('custom_services', '<small style="color: red">:message</small>') as $error)
            {!! $error !!}
        @endforeach
    </div>
    <add-item-button
        class="addition-link"
        name="custom_services"
        items="{{ json_encode(old('custom_services') ?? $existingCustomServices ?? (session('registerFormData.custom_services') ?? [])) }}"
    >{{ __('main.add-service') }}</add-item-button>
</div>

<div class="form__column">
    {!! $serviceCategory->customers_text !!}
</div>

<div class="form__column">
    {!! $serviceCategory->capabilities_text !!}
</div>

<div class="form__column">
    <div class="registration-title">{{ __('auth.register.capabilities') }}</div>
    <div class="form__row--error">
        @foreach($errors->get('capabilities', '<small style="color: red">:message</small>') as $error)
            {!! $error !!}
        @endforeach
    </div>

    @foreach ($serviceCategory->capabilities as $service)
        @if (!$service->title) @continue @endif
        <div class="form__column">
            <div class="form__row" @if (!empty($service->input_label)) data-component="step2ConditionnalCheckboxInput" @endif>
                <sl-checkbox
                    name="capabilities[]"
                    value="{{ $service->id }}"
                    @if (in_array($service->id, old('capabilities') ?? $existingCapabilities ?? (session('registerFormData.capabilities') ?? []))) checked @endif
                >
                    {{ $service->title }}
                </sl-checkbox>
                @if (!empty($service->input_label))
                    <input type="text" 
                           name="capability_input[{{ $service->id }}]" 
                           value="{{ old('capability_input.' . $service->id) ?? ($existingCapabilityInputs[$service->id] ?? (session('registerFormData.capability_input.' . $service->id) ?? '')) }}"
                           placeholder="{{ $service->input_label }}"
                    >
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="form__column">
    <div class="form__row--error">
        @foreach($errors->get('custom_capabilities', '<small style="color: red">:message</small>') as $error)
            {!! $error !!}
        @endforeach
    </div>
    <add-item-button
        class="addition-link"
        name="custom_capabilities"
        items="{{ json_encode(old('custom_capabilities') ?? $existingCustomCapabilities ?? (session('registerFormData.custom_capabilities') ?? [])) }}"
    >{{ __('main.add-capability') }}</add-item-button>
</div>
