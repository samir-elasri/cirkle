<?php

namespace App\Http\Controllers;

use App\Models\Core\MenuTree;
use App\Models\Core\Page;
use App\Models\Core\Setting;
use Carbon\Carbon;
use URL;

trait StructuredDataTrait
{

	public function getStructuredDatas($page)
	{
		$structured_datas = [];
		$blocs = $page->blocs;

		foreach ($blocs as $bloc) {
			$bloc->blocable->bloc = $bloc;
			$curr = (object) array_merge($bloc->toArray(), $bloc->blocable->toArray());
			$class_name = class_basename($curr->blocable_type);
			$function = 'createDataFrom' . $class_name;

			if (method_exists($this, $function)) {
				$this->$function($curr, $structured_datas);
			}
		}

		$this->addOrganization($structured_datas);
		$this->addSearch($structured_datas);
		$this->addBreadCrumb($structured_datas, $page);
		return $structured_datas;
	}

	public function createDataFromNewsList($newsList)
	{
		$data = [];
		foreach ($newsList as $news) {
			array_push($data, self::createDataFromNews($news));
		}
		return $data;
	}

	public function createDataFromNews($news)
	{
		$publication_date = Carbon::parse($news->publication_date);
		$official_date = Carbon::parse($news->official_date);
		$structured_data = [
			'@context' => 'https://schema.org',
			'@type' => 'NewsArticle',
			'image' => URL::to('/') . $news->image,
			'datePublished' => $publication_date->toIso8601String(),
			'dateModified' => $official_date->toIso8601String(),
			'headline' => wordLimit($news->title, 110),
			'mainEntityOfPage' => [
				'@type' => 'WebPage',
				'@id' => URL::to('/') . Page::getUrlByCustomCode('news-list'),
			],
		];
		$structured_data += $this->getAuthor();
		$structured_data += $this->getPublisher();
		return [$structured_data];
	}

	public function createDataFromBasicEvents($eventsList)
	{
		$data = [];
		foreach ($eventsList as $event) {
			array_push($data, self::createDataFromBasicEvent($event));
		}
		return $data;
	}

	public function createDataFromBasicEvent($event)
	{
		$publication_date = Carbon::parse($event->publication_date);
		$official_date = Carbon::parse($event->official_date);
		$structured_data = [
			'@context' => 'https://schema.org',
			'@type' => 'EventArticle',
			'image' => URL::to('/') . $event->image,
			'datePublished' => $publication_date->toIso8601String(),
			'dateModified' => $official_date->toIso8601String(),
			'headline' => wordLimit($event->title, 110),
			'mainEntityOfPage' => [
				'@type' => 'WebPage',
				'@id' => URL::to('/') . Page::getUrlByCustomCode('basic-event-list'),
			],
		];
		$structured_data += $this->getAuthor();
		$structured_data += $this->getPublisher();
		return [$structured_data];
	}



	public function createDataFromBlocGallery($bloc, &$data)
	{

		$gallery = $bloc->blocable;
        $thumb = null;
		foreach ($gallery['elements'] as $element) {
			if ($element['type_element'] == 'youtube') {
				$url = getYoutubeUrl($element['filename']);
				$thumb = getYoutubeThumb($url);
			} else if ($element['type_element'] == 'vimeo') {
				$url = $element['filename'];
				$thumb = getVimeoThumb($url);
			} else if ($element['type_element'] == 'locale') {
				$url = url($element['filename']);
				$thumb = url($element['image']);
			}
			if (!empty($url)) {

				$structured_data = [
					'@context' => 'https://schema.org',
					'@type' => 'VideoObject',
					'name' => $element['legend'],
					'description' => strip_tags($element['description']),
					'thumbnailUrl' => $thumb,
					'uploadDate' => Carbon::parse($element['created_at'])->toIso8601String(),
					'contentUrl' => $url,
				];

				array_push($data, $structured_data);
				unset($url);
			}
		}
	}

	public function createDataFromBlocPortfolio($bloc, &$data)
	{
		$gallery = $bloc->blocable;
        $thumb = null;
		foreach ($gallery['elements'] as $element) {
			if ($element['type_element'] == 'youtube') {
				$url = getYoutubeUrl($element['filename']);
				$thumb = getYoutubeThumb($url);
			} elseif ($element['type_element'] == 'vimeo') {
				$url = $element['filename'];
				$thumb = getVimeoThumb($url);
			} elseif ($element['type_element'] == 'locale') {
				$url = url($element['filename']);
				$thumb = url($element['image']);
			}
			if (!empty($url)) {
				$structured_data = [
					'@context' => 'https://schema.org',
					'@type' => 'VideoObject',
					'name' => $element['legend'],
					'description' => strip_tags($element['description']),
					'thumbnailUrl' => $thumb,
					'uploadDate' => Carbon::parse($element['created_at'])->toIso8601String(),
					'contentUrl' => $url,
				];

				array_push($data, $structured_data);
				unset($url);
			}
		}
	}

	public function createDataFromBlocText($bloc, &$data)
	{
        $thumb = null;
		if ($bloc->media_type == 'youtube') {
			$url = getYoutubeUrl($bloc->video_url);
			$thumb = getYoutubeThumb($url);
		} else if ($bloc->media_type == 'vimeo') {
			$url = $bloc->video_url;
			$thumb = getVimeoThumb($url);
		} else if ($bloc->media_type == 'video') {
			$url = url($bloc->video_filename);
			$thumb = url($bloc->image);
		}
		if (!empty($url)) {

			$structured_data = [
				'@context' => 'https://schema.org',
				'@type' => 'VideoObject',
				'name' => $bloc->legend,
				'description' => strip_tags($bloc->content),
				'thumbnailUrl' => $thumb,
				'uploadDate' => Carbon::parse($bloc->created_at)->toIso8601String(),
				'contentUrl' => $url,
			];

			array_push($data, $structured_data);
		}
	}

	public function createDataFromBlocVideo($bloc, &$data)
	{
		$url = $bloc->video_url;
		if ($bloc->video_type == 'youtube') {
			$thumb = getYoutubeThumb($url);
			$url = getYoutubeUrl($url);
		} else if ($bloc->video_type == 'vimeo') {
			$thumb = getVimeoThumb($url);
		} else {
			$thumb = $bloc->video_filename;
			$url = url($url);
		}

		$structured_data = [
			'@context' => 'https://schema.org',
			'@type' => 'VideoObject',
			'name' => $bloc->title,
			'description' => strip_tags($bloc->description),
			'thumbnailUrl' => url($thumb),
			'uploadDate' => Carbon::parse($bloc->created_at)->toIso8601String(),
			'contentUrl' => $url,
		];

		array_push($data, $structured_data);
	}

	public function createDataFromBlocMiniCard($bloc, &$data)
	{
		$group = $bloc->group->cards;
		$list = [];
		if (!empty($group)) {
			$list = [
				'@context'        => 'https://schema.org',
				'@type'           => 'ItemList',
				'itemListElement' => [],
			];
		}
		foreach ($group as $key => $card) {
			$item = [
				'@type' => 'ListItem',
				'position' => $key + 1,
				'url' => $card->call_to_action_url,
				'image' => url($card->image),

			];

			$list['itemListElement'][] = $item;
		}

		$data[] = $list;
	}

	public function getAuthor()
	{
		return [
			'author' => [
				'name' => setting('company_name'),
				'@type' => 'Organization'
			],
		];
	}

	public function getPublisher()
	{
		return [
			'publisher' => [
				'name' => setting('company_name'),
				'@type' => 'Organization',
				'logo' => [
					'@type' => 'ImageObject',
					'url' => URL::to('/') . setting()->main_logo_image
				],
			],
		];
	}

	public function addOrganization(&$datas)
	{
		$orga = [
			'@context'     => 'https://schema.org',
			'@type'        => 'Organization',
			'url'          => URL::to('/'),
			'logo'         => URL::to('/') . setting()->main_logo_image,
			'contactPoint' => [
				'@type' => 'ContactPoint',
				'telephone' => !empty(setting()->corpo_statement) ? setting()->corpo_statement : '+1 514 273-2166',
				'contactType' => 'customer service'
			],
		];

		array_push($datas, $orga);
	}

	public function addSearch(&$datas)
	{
		$search = [
			'@context'        => 'https://schema.org',
			'@type'           => 'WebSite',
			'url'             => url(''),
			'potentialAction' => [
				'@type'       => 'SearchAction',
				'target'      => Page::getUrlByCustomCode('search-results') . '?q={search_term_string}',
				'query-input' => 'required name=search_term_string'
			],
		];

		array_push($datas, $search);
	}

	public function addBreadcrumb(&$datas, $page, $breadcrumb = [])
	{
		$breadcrumb[] = $page;
		$menu = MenuTree::where('page_id', $page->id)->first();

		if (!empty($menu->parent_id) && !empty($parent = MenuTree::find($menu->parent_id)) && $page = Page::find($parent->page_id)) {
			return self::addBreadcrumb($datas, $page, $breadcrumb);
		}

		$breadcrumb = array_reverse($breadcrumb);

		$structured = [
			'@context' => 'https://schema.org',
			'@type' => 'BreadcrumbList',
			'itemListElement' => [],
		];

		foreach ($breadcrumb as $key => $loopPage) {
			$structured['itemListElement'][] = [
				'@type'    => 'ListItem',
				'position' => $key + 1,
				'name'     => $loopPage->title,
				'item'     => url($loopPage->url),
			];
		}

		$datas[] = $structured;
	}
}
