<?php

function fMoney($num, $noHtml = false)
{
	$int = substr(number_format($num, 2), 0, -3);
	$float = substr(number_format($num, 2), -2);
	if ($noHtml) {
		return $int . '.' . $float . '$';
	}

	return '<span class="fMoney">' . '<span>' . $int . '</span>' . '<span>.</span>' . '<span>' . $float . '</span>' . '<span>$</span>' . '</span>';
}

function prettyPrice($price)
{
	if (app()->getLocale() === 'fr') {
		return number_format((float) $price, 2, ',', ' ')  . ' $';
	}

	return '$' . number_format((float) $price, 2, '.', ',');
}
