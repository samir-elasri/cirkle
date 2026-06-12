{!! $blocs !!}

<section>
    <div class="optimal-content-width">
        <div class="provider-search">
            <div class="provider-search__results">
                @foreach ($subscribers as $provider)
                    @include ('partials.providers.single')
                @endforeach
            </div>
        </div>
    </div>
</section>
