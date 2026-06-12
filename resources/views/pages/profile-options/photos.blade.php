<section>
    <div class="optimal-content-width">
        <div data-component="profileOptionList"
             style="gap: 15px; display:flex; flex-direction: column;">
            <script type="application/json">{!! json_encode([
                            'getList' => urlRouteName('photo-list'),
                            'moveUrl' => urlRouteName('option-move', ['type' => 'subscriber_images'], true),
							'deleteUrl' => urlRouteName('option-delete', ['type' => 'subscriber_images']),
							'isPhotos' => true,
                            'deleteModalDetails' => [
								'cancel' => trans('main.delete-modal.cancel'),
								'title' => trans('main.delete-modal.title'),
								'text' => trans('main.delete-modal.text'),
								'confirm' => trans('main.delete-modal.confirm'),
],
                            'editLegendModalDetails' => [
								'title' => trans('main.edit-modal'),
],
'editUrl' => urlRouteName('edit-legend')
		                ], JSON_THROW_ON_ERROR) !!}</script>
            <div class="single-list"
                 data-ref="list">
                @include('partials.profile-options.photo-list', ['data' => $data])
            </div>
        </div>

        @include('core.partials.spacing', ['spacing' => 20])

        @include('partials.profile-options.add-option-modal', ['optionName' => 'subscriber_images'])

    </div>
</section>