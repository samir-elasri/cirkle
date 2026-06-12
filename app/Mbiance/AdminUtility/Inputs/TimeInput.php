<?php

namespace Mbiance\AdminUtility\Inputs;

use Mbiance\AdminUtility\Inputs\Contracts\Dateable;

class TimeInput extends Input implements Dateable
{
	protected function getInput(): string
	{
//		$this->options['class'] .= ' timepicker';
		$this->options['style'] = 'padding-right:unset';
		$this->options['placeholder'] = '00:00:00';
		$this->options['step'] = '1';

		$time = !is_null($this->value) ? $this->parseDate($this->value) : $this->value;

		$str = "<div class=\"col-md-3\" style=\"width:160px;padding-left: 0;\">\n";
		$str .= "	<div class=\"input-group bootstrap-timepicker col-md-12\">\n";
		$str .= "	<span class=\"input-group-addon add-on\"><i class=\"fa fa-clock-o\"></i></span>\n";
		$str .= '       ' . $this->generateForm('time', $this->name, $time, $this->options) . "\n";
		$str .= "	</div>\n";
		$str .= "</div>\n";

		return $str;
	}

	public function parseDate($value): string
	{
		return $value ? date('H:i:s', strtotime($value)) : '';
	}

	private function isDateString($value)
	{
		return $value === null;
	}
}
