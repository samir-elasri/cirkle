<?php

namespace Mbiance\AdminUtility\Inputs;

class PercentInput extends Input
{
	protected function getInput() : string
	{
		$this->value = !empty($this->value) ? $this->value : 0;
		$str = "<div class=\"input-prepend input-group col-sm-3\">\n";
		$str .= "	<span class=\"input-group-addon\"><b>%</b></span>\n";
		$str .= '   ' . $this->generateForm('text', $this->name, $this->value, $this->options) . "\n";
		$str .= "</div>\n";

		return $str;
	}
}