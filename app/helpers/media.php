<?php

function getYoutubeId($url)
{
	preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
	$id = !empty($matches[0]) ? $matches[0] : $url;
	return $id;
}

function getVimeoId($url)
{
	$id = (int) substr(parse_url($url, PHP_URL_PATH), 1);
	return $id;
}

function getYoutubeUrl($url)
{
	$id = getYoutubeId($url);
	return "//www.youtube.com/watch?v=$id";
}

function getVimeoThumb($url)
{
	$id = getVimeoId($url);
	$vimeo = unserialize(@file_get_contents("//vimeo.com/api/v2/video/$id.php"));
	return $vimeo[0]['thumbnail_large'];
}

function getYoutubeThumb($url)
{
	return 'https://img.youtube.com/vi/' . getYoutubeId($url) . '/maxresdefault.jpg';
}
