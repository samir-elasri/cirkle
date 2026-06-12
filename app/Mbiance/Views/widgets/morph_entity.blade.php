<?php
	$morphs = [];
	foreach($options['morph_classes'] as $element) {
		$morphs[$element] = $element::all()->pluck('id', 'title')->all();
	}
?>
{!! FormUtility::select($options['morph_type_name'], Arr::get($obj['niceNames'], $options['morph_type_name']), $options['morph_classes']) !!}
{!! FormUtility::select($field, $label, ['' => '']) !!}

<script type="text/javascript">
	var morphs = {{ json_encode($morphs) }}
	var morphId = {{ $field }}
	var selectedMorphId = {{ json_encode($obj[$field]) }}
	var morphType = {{ str_replace('id', 'type', $field) }};

	var generateOptions = function(morphs, morphId) {
		morphs = (morphs === undefined) ? {} : morphs;

		$("option", morphId).remove();
		var morphArray = $.makeArray(morphs);

		$.map(Object.keys(morphs), function(value, key) {
			var selected = (selectedMorphId == morphs[value]) ? 'selected="selected"' : '';

			$(morphId).append('<option value="'+ morphs[value] + '" ' + selected + '>' + value + '</option>');
		});
	}

	var classToLoad = $(morphType).find(":selected").val();
	var newMorphs = morphs[classToLoad];

	generateOptions(newMorphs, morphId);

	$(morphType).change(function(event) {
		var newMorphs = morphs[event.target.value];

		generateOptions(newMorphs, morphId);
	});


</script>