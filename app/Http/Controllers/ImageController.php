<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;


class ImageController extends Controller
{


	/**
	 * Display the specified resource.
	 *
	 * @param  Request  $request
	 * @param $source
	 * @return mixed
	 */
	public function show(Request $request, $source)
	{
		$maxWidth = $request->get('w');
		$maxHeight = $request->get('h');
		$crop  = $request->get('c');

		$image = Image::cache(static function ($image) use ($maxHeight, $maxWidth, $source, $crop) {
			/** @var \Intervention\Image\Image $image */
			$image->make($source);

			if ($maxHeight !== null || $maxWidth !== null) {

				if ($crop) {
					// The following two lines is equivalent to finding if one value is null and setting it to the other
					$maxWidth = $maxWidth ?: $maxHeight;
					$maxHeight = $maxHeight ?: $maxWidth;

					$image->fit($maxWidth, $maxHeight, static function ($constraint) {
						$constraint->upsize();
					});

				} else {

					$image->resize($maxWidth, $maxHeight, static function ($constraint) {
						/** @var Constraint $constraint */
						$constraint->aspectRatio();
						$constraint->upsize();
					});
				}
			}
		}, 10, true);

		return $image->response();
	}
}
