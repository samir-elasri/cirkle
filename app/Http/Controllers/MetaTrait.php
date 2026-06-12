<?php

namespace App\Http\Controllers;

use App\Models\Core\Sharing;
use URL;

trait MetaTrait
{

	/*
	* Create meta and title
	*/
	public function createMeta($page_title, $title = '', $description = '', $image = ''): object
	{
		if (empty($title)) {
			$title = $page_title;
		}

		$description = $description ? wordLimit(strip_tags($description), 150) : '';

		return (object) [
			'title' => $title,
			'description' => $description,
			'fb_title' => $title,
			'fb_description' => $description,
			'fb_image' => URL::to('/') . $image,
			'tw_title' => $title,
			'tw_description' => $description,
			'tw_image' => URL::to('/') . $image,
		];
	}

	/*
	* Create meta and title
	*/
	public function createMetaFromPage($page): object
	{

		$metadata = $this->createMeta($page->title, $page->meta_title, $page->meta_description, $page->meta_image);

		/** @var Sharing $sharing */
		$sharing = $page->sharing;

		$metadata->fb_title = $sharing->fb_title ?? $metadata->title;
		$metadata->fb_description = $sharing->fb_description ?? $metadata->description;
		$metadata->fb_image = $sharing->fb_image ?? $metadata->fb_image;

		$metadata->tw_title = $sharing->tw_title ?? $metadata->title;
		$metadata->tw_description = $sharing->tw_description ?? $metadata->description;
		$metadata->tw_image = $sharing->tw_image ?? $metadata->tw_image;

		return $metadata;
	}
}
