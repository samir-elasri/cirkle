<?php

namespace Mbiance\AdminUtility\Inputs;

class SelectInput extends Input
{
	public function __construct($form, $params)
	{
		$this->form = $form;
		$this->name = $params[0];
		$this->list = $params[1];
		$this->value = $params[2];
		$this->options = $params[3];
	}

	protected function getInput(): string
	{
		return '       ' . $this->generateForm('select', $this->name, $this->list, $this->value, $this->options) . "\n";
	}
}
