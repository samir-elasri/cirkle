<?php

namespace Mbiance\AdminUtility\Inputs;

class MoneyInput extends Input
{
	protected $type = 'text';

	protected function getInput() : string
	{
		if (empty($this->value)) $this->value = 0;

		$this->value = number_format($this->value, 2, '.', '');
		$str = "<div class=\"input-prepend input-group col-sm-3\">\n";
		$str .= "	<span class=\"input-group-addon\"><i class=\"fa fa-dollar\"></i></span>\n";
		$str .= '   ' . $this->generateForm('text', $this->name, $this->value, $this->options) . "\n";
		$str .= "</div>\n";

		return $str;
	}
}