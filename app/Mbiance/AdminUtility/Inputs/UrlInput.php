<?php

namespace Mbiance\AdminUtility\Inputs;

class UrlInput extends Input
{
	protected function getInput() : string
	{
		$str = "<div class=\"input-prepend input-group\">\n";
		$str .= "	<span class=\"input-group-addon\"><i class=\"fa fa-link\"></i></span>\n";
		$str .= '   ' . $this->generateForm('url', $this->name, $this->value, $this->options) . "\n";
		$str .= "</div>\n";

		return $str;
	}
}