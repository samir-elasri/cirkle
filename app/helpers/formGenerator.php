<?php

if (!function_exists('parseMimes')) {

	function parseMimes(string $mimesString): string
	{
		return str_replace(' ', '', str_replace(';', ',', $mimesString));
	}
}

if (!function_exists('is_serialized')) {

	function is_serialized($value, &$result = null)
	{
		// Bit of a give away this one
		if (!is_string($value) || $value === '') {
			return false;
		}

		// we only serialize array
		return @unserialize($value, ['allowed_classes' => true]) !== false;
	}
}
