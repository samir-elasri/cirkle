<?php

namespace Mbiance\AdminUtility\Inputs;

class TextareaInput extends Input
{
	protected function getInput() : string
	{
		if (!isset($this->options['rows'])) {
			$this->options['rows'] = '4';
			$this->options['style'] = 'min-height:150px;';
		}

		return '       ' . $this->generateForm('textarea', $this->name, $this->value, $this->options) . "\n";
	}
}