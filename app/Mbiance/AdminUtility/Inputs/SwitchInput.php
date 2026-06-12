<?php

namespace Mbiance\AdminUtility\Inputs;

class SwitchInput extends Input
{
	protected function getInput(): string
	{
		$str = '<input type="hidden" name="' . $this->name . "\" value=\"0\">\n";
		$str .= '<div class="make-switch" data-on="success" data-off="default">' .
			$this->generateForm('checkbox', $this->name, 1, $this->value) . "</div>\n";

		return $str;
	}
}
