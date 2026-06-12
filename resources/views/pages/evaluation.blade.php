<section>
    <div class="optimal-content-width">

        <h3>
            {{ __('evaluation.for-provider') }}: {{ $provider->name }}
        </h3>
        <h3>
            {{ __('evaluation.contact-date') }}: {{ prettyDate($contact?->created_at) }}
        </h3>

        {{ Form::open(['url' => urlRouteName('evaluation.store'), 'class' => 'full-screen-form']) }}

        {{ Form::hidden('provider_id', $provider->id) }}
        {{ Form::hidden('client_id', $client->id) }}
        @foreach($errors->get('provider_id', '<small style="color: red">:message</small>') as $error)
            {!! $error !!}
        @endforeach
        @foreach($errors->get('client_id', '<small style="color: red">:message</small>') as $error)
            {!! $error !!}
        @endforeach

        <div class="form__row">
            <div class="form__column">
                {{__('evaluation.global_grade')}}
            </div>
            @foreach($errors->get('global_grade', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            <div class="form__column">
                @include('partials.starSelector', ['name' => 'global_grade'])

            </div>
        </div>

        <div class="form__row">
            <div class="form__column">
                {{__('evaluation.service_quality_grade')}}
            </div>
            @foreach($errors->get('service_quality_grade', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            <div class="form__column">
                @include('partials.starSelector', ['name' => 'service_quality_grade'])

            </div>
        </div>

        <div class="form__row">
            <div class="form__column">
                {{__('evaluation.reliability_grade')}}
            </div>
            @foreach($errors->get('reliability_grade', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            <div class="form__column">
                @include('partials.starSelector', ['name' => 'reliability_grade'])

            </div>
        </div>

        <div class="form__row">
            <div class="form__column">
                {{__('evaluation.communication_grade')}}
            </div>
            @foreach($errors->get('communication_grade', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            <div class="form__column">
                @include('partials.starSelector', ['name' => 'communication_grade'])

            </div>
        </div>

        <div class="form__row">
            <div class="form__column">
                {{__('evaluation.hourly_rate_grade')}}
            </div>
            @foreach($errors->get('hourly_rate_grade', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            <div class="form__column">
                @include('partials.starSelector', ['name' => 'hourly_rate_grade'])

            </div>
        </div>

        <div class="form__row">
            <div class="form__column">
                {{__('evaluation.comment')}}
            </div>
            @foreach($errors->get('comment', '<small style="color: red">:message</small>') as $error)
                {!! $error !!}
            @endforeach
            <div class="form__column">
                {{ Form::textarea('comment') }}</div>
        </div>

        <button type="submit"
                class="call-to-action">{{ __('evaluation.submit') }}</button>
        {{ Form::close() }}
    </div>
</section>
