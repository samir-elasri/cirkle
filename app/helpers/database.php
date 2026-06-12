<?php

function gatherTranslatables($data)
{
	$locales = getLocales();

	foreach ($data as $key => $value) {
		if (is_array($value) && !in_array($key, $locales)) {
			unset($data[$key]);
		}
	}

	return $data;
}
