<?php

function prettySize($size, $unit = null)
{
	if ((!$unit && $size >= 1 << 30) || $unit == 'Go')
		return number_format($size / (1 << 30), 2) . 'Go';
	if ((!$unit && $size >= 1 << 20) || $unit == 'Mo')
		return number_format($size / (1 << 20), 2) . 'Mo';
	if ((!$unit && $size >= 1 << 10) || $unit == 'Ko')
		return number_format($size / (1 << 10), 2) . 'Ko';
	return number_format($size) . ' octets';
}
