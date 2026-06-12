<?php

function array_changes(array $before, array $after): array {

	$changes = [];

	$keys = array_keys(
		array_replace($before, $after)
	);

	foreach($keys as $key) {
		$beforeValue = $before[$key] ?? null;
		$afterValue = $after[$key] ?? null;

		if(is_array($beforeValue) || is_array($afterValue)) {
			if ($arr = array_changes($beforeValue ?? [], $afterValue ?? [])) {
				$changes[$key] = $arr;
			}
		} else if($afterValue !== null && $afterValue !== '' && $beforeValue !== $afterValue) {
			$changes[$key] = $afterValue;
		}
	}

	return $changes;
}

function array_default(&$arr, $key, $default, $undefined = null)
{
	$val = Arr::get($arr, $key);
	if ($val === $undefined)
		$arr[$key] = $default;
}

function fwrite_array($f, $arr, $indent = 0)
{

	$tabs = str_pad('', $indent - 1, "\t");
	$tabs2 = $tabs . "\t";

	fwrite($f, "[\n");

	foreach ($arr as $key => $val) {

		fwrite($f, $tabs2 . (is_string($key) ? "'" . addslashes($key) . "' => " : ''));

		switch (gettype($val)) {

			case 'array':
			case 'object':
				fwrite_array($f, $val, $indent + 1);
				break;

			case 'boolean':
				fwrite($f, ($val ? 'true' : 'false'));
				break;

			case 'string':
				fwrite($f, '\'' . str_replace('\'', '\\\'', $val) . '\'');
				break;

			case 'NULL':
				fwrite($f, 'null');
				break;

			case 'double':
			case 'integer':
				fwrite($f, $val);
				break;
		}

		fwrite($f, ",\n");
	}

	fwrite($f, $tabs . ']');
}

function array_php_file($filename, $arr)
{
	$f = fopen($filename, 'wb');

	fwrite($f, '<?php return ');
	fwrite_array($f, $arr, 1);
	fwrite($f, ';');

	fclose($f);
}
