<div data-component="optionList">
    <div data-ref="list">
        @include('partials.profile-options.diploma-list', ['data' => $optionData])
    </div>
</div>
@include('partials.profile-options.add-option-modal', ['optionName' => $optionName])
