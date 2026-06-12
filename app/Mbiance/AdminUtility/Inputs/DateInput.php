<?php

namespace Mbiance\AdminUtility\Inputs;

use Carbon\Carbon;
use Mbiance\AdminUtility\Inputs\Contracts\Dateable;

class DateInput extends Input implements Dateable
{
	protected function getInput(): string
	{
		$date = $this->parseDate($this->value);

		$this->options['class'] .= ' date-picker';
		$this->options['data-date-format'] = 'yyyy-mm-dd';
		$this->options['style'] = 'padding-right:unset';
		$this->options['autocomplete'] = 'off';

        $str = "<div class=\"col-md-3\" style=\"width:160px;padding-left: 0px;\">\n";
		$str .= "	<div class=\"input-group date col-md-12\">\n";
		$str .= "	<span class=\"input-group-addon\"><i class=\"fa fa-calendar\"></i></span>\n";
		$str .= '       ' . $this->generateForm('text', $this->name, $date, $this->options) . "\n";
		$str .= "	</div>\n";
		$str .= "</div>\n";

		return $str;
	}

	public function parseDate($value): string
	{
		return empty($value) ? '' : Carbon::parse($value)->format('Y-m-d');
	}
}
