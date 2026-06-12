<?php

namespace Mbiance\AdminUtility\Inputs;

class PasswordInput extends Input
{
	public function __construct($form, $params)
	{
		$this->form = $form;
		$this->name = $params[0];
		$this->options = $params[1];
	}

	protected function getInput() : string
	{
		$str = "<div class=\"input-prepend input-group\">\n";
		$str .= "	<span class=\"input-group-addon\"><i class=\"fa fa-key\"></i></span>\n";
		$str .= '       ' . $this->generateForm('password', $this->name, $this->options) . "\n";
		$str .= "</div>\n";

		return $str;
	}
}