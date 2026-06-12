<?php

namespace Mbiance\AdminUtility\Inputs;

class EmailInput extends Input
{
	protected function getInput() : string
	{
		$str = '';
		$str .= "<div class=\"input-prepend input-group\">\n";
		$str .= "	<span class=\"input-group-addon\"><i class=\"fa fa-envelope-o\"></i></span>\n";
		$str .= '   ' . $this->generateForm('email', $this->name, $this->value, $this->options) . "\n";
		$str .= "</div>\n" . "\n";

		return $str;
	}
}