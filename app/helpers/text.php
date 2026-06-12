<?php

function splitWordToSpan($content, $cssClass = null) {
    $desc = strip_tags($content);
    $words = explode(' ', $desc);
    $newContent = '';
    if($cssClass){
        foreach($words as $word) $newContent .= '<span class="'. $cssClass .'">' . $word . ' </span>';
    } else {
        foreach($words as $word) $newContent .= '<span>' . $word . ' </span>';
    }

    return $newContent;
}

function trim_text($text, $length, $ellipses = '...') {

	$text = strip_tags($text);

    if (strlen($text) <= $length) return $text;

    $last_space = strrpos(substr($text, 0, $length), ' ');
    $trimmed_text = substr($text, 0, $last_space);

    $trimmed_text .= $ellipses;

    return $trimmed_text;
}

function wordLimit($content, $charLimit, $clipped = '...') {
    if(strlen($content) < $charLimit) return $content;
	$parts = preg_split('/([\s\n\r]+)/u', $content, null, PREG_SPLIT_DELIM_CAPTURE);
	$parts_count = count($parts);
	$length = 0;
	$last_part = 0;
	for (; $last_part < $parts_count; ++$last_part) {
		$length += strlen($parts[$last_part]);
		if ($length > $charLimit) { break; }
	}
	$content = rtrim(implode(array_slice($parts, 0, $last_part))) . $clipped;
	return $content;
}


function null_or_empty_string($value) {
	return $value === null || trim($value) === '';
}

function slug($str) {
	return Str::slug($str);
}
