<?php

use Carbon\Carbon as Carbon;

function timeFormat($time)
{
	return date('G\hi', strtotime($time));
}

function frenchDateFormat(string $date)
{
	return implode(
		' / ',
		array_reverse(
			explode('-', $date)
		)
	);
}

if (! function_exists('carbon')) {
	/**
	 * Create a new Carbon instance for the given datetime and/or timezone.
	 *
	 * @param  \DateTime|string|null $datetime
	 * @param  \DateTimeZone|string|null $tz
	 * @return \Illuminate\Support\Carbon
	 */
	function carbon($datetime = null, $tz = null)
	{
		if ($datetime instanceof \DateTime) {
			return \Illuminate\Support\Carbon::instance($datetime)->setTimezone($tz);
		}

		return \Illuminate\Support\Carbon::parse($datetime, $tz);
	}
}

/**
 * @param $date
 * @return Carbon
 */
function createDateTime($date)
{
	if (gettype($date) == 'integer') {
		$dt = Carbon::createFromTimestamp($date);
	} else if (is_a($date, 'DateTime')) {
		$dt = Carbon::instance($date);
	} else if (is_object($date) && isset($date->date)) {
		$dt = Carbon::parse($date->date);
	} else {
		$dt = Carbon::parse($date);
	}
	return $dt;
}

function prettyDate($date, $format = null)
{
	if (empty($date)) {
		return '';
	}

	return createDateTime($date)->prettyDate($format);
}

/** @noinspection AllyPlainPhpInspection */
function prettyDateTime($date, $format = null)
{
	if (empty($date)) {
		return '';
	}

	return createDateTime($date)->prettyDateTime($format);
}

function prettyTime($time, $format = null): string
{
	if (empty($time)) {
		return '';
	}

	return today()->setTimeFromTimeString($time)->prettyTime($format);
}

function w3cDate($date)
{
	return createDateTime($date)->toW3cString();
}

function timestamp($date)
{
	if (is_a($date, 'DateTime')) {
		$dt = Carbon::instance($date);
	} else {
		$dt = Carbon::parse($date);
	}
	return $dt;
}

function getHTMLTagTime($dateStart, $dateEnd = null)
{

	if (!isset($dateEnd) || prettyDate($dateStart) == prettyDate($dateEnd)) {
		return '<time datetime="' . w3cDate($dateStart) . '">' . prettyDate($dateStart) . '</time>';
	} else {
		$html = '';
		if (prettyDate($dateStart, '%B_%Y') == prettyDate($dateEnd, '%B_%Y')) { // same month of same year
			$html .= '<time datetime="' . w3cDate($dateStart) . '">' . prettyDate($dateStart, '%e') . '</time>';
		} else if (prettyDate($dateStart, '%Y') == prettyDate($dateEnd, '%Y')) { // same year
			$html .= '<time datetime="' . w3cDate($dateStart) . '">' . prettyDate($dateStart, '%e  %b') . '</time>';
		} else {
			$html .= '<time datetime="' . w3cDate($dateStart) . '">' . prettyDate($dateStart) . '</time>';
		}
		$html .=  ' au ' . '<time datetime="' . w3cDate($dateEnd) . '">' . prettyDate($dateEnd) . '</time>';
		return $html;
	}
}
