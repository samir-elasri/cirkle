<?php

namespace Mbiance\AdminUtility\Inputs;

class TextInput extends Input
{
	protected $type = 'text';

	protected function getInput() : string
	{
		return '       ' . $this->generateForm('input', $this->type, $this->name, $this->value, $this->options) . "\n";
	}
}