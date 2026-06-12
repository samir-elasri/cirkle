<section>
    <div class="optimal-content-width">
        @if (!isset($registrationForm))
            {{Form::open(['url' => urlRouteName('url.update')])}}
        @endif

        <div class="form__column">
            {{Form::label('fr[url]', trans('profile.options.fields.fr_url'))}}
            {{Form::input('text', 'fr[url]', $data->translate('fr')?->url)}}
        </div>
        <div class="form__column">
            {{Form::label('en[url]', trans('profile.options.fields.en_url'))}}
            {{Form::input('text', 'en[url]', $data->translate('en')?->url)}}
        </div>

        @if (!isset($registrationForm))
            {{Form::submit(trans('profile.options.update-url'), ['class' => 'call-to-action'])}}
            {{Form::close()}}
        @endif
    </div>
</section>