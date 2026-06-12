<?php

namespace Mbiance\AdminUtility\Inputs;

class NumberInput extends Input
{
	protected function getInput() : string
	{
		return '       ' . $this->generateForm('number', $this->name, $this->value, $this->options) . "\n";
	}
}