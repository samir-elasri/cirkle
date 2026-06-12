<section>
    <div class="optimal-content-width">
        <div data-component="profileOptionList" style="gap: 15px; display:flex; flex-direction: column;">
            <script type="application/json">{!! json_encode([
                'getList' => urlRouteName('license-list'),
                'moveUrl' => urlRouteName('option-move', ['type' => 'licenses'], true),
                'deleteUrl' => urlRouteName('option-delete', ['type' => 'licenses']),
                'deleteModalDetails' => [
                    'cancel' => trans('main.delete-modal.cancel'),
                    'title' => trans('main.delete-modal.title'),
                    'text' => trans('main.delete-modal.text'),
                    'confirm' => trans('main.delete-modal.confirm'),
                ]
            ], JSON_THROW_ON_ERROR) !!}</script>
        <div class="single-list" data-ref="list">
            @include('partials.profile-options.license-list', ['data' => $data])
        </div>
        </div>

        @include('core.partials.spacing', ['spacing' => 20])

        @include('partials.profile-options.add-option-modal', ['optionName' => 'license'])

    </div>
</section>
