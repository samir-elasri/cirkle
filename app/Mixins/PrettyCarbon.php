<?php
/**
 * @noinspection PhpDocSignatureInspection
 * @noinspection PhpIncompatibleReturnTypeInspection
 */

namespace App\Mixins;


use Carbon\Carbon;


class PrettyCarbon
{
	/**
	 * @param $format
	 * @return string
	 */
	public function prettyDate()
	{
		return function ($format = null) {
			if (!$format) {
				$i = app()->getLocale();
				if ($i === 'en') {
					$format = 'MMMM D YYYY';
				} else {
					$format = 'D MMMM YYYY';
				}
			}

			/** @var Carbon $this */
			return $this->isoFormat($format);
		};
	}

	/**
	 * @param $format
	 * @return string
	 */
	public function prettyDateTime()
	{
		return function ($format = null) {
			if (!$format) {
				$i = app()->getLocale();
				if ($i === 'en') {
					$format = 'MMMM D YYYY - HH:mm';
				} else {
					$format = 'D MMMM YYYY - HH[H]mm';
				}
			}

			/** @var Carbon $this */
			return $this->isoFormat($format);
		};
	}

	/**
	 * @param $format
	 * @return string
	 */
	public function prettyTime()
	{
		return function ($format = null) {
			if (!$format) {
				$i = app()->getLocale();
				if ($i === 'en') {
					$format = 'HH:mm';
				} else {
					$format = 'HH[H]mm';
				}
			}

			/** @var Carbon $this */
			return $this->isoFormat($format);
		};
	}
}
