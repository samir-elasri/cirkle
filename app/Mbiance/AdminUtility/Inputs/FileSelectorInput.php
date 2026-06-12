<?php

namespace Mbiance\AdminUtility\Inputs;

class FileSelectorInput extends Input
{
	protected $type = 'file_selector';

	public function __construct($form, $params)
	{
		$this->form = $form;
		$this->name = $params[0];
		$this->value = $params[1];
		$this->options = $params[2];
		$this->isRequired = isset($params[3]) ? $params[3] : false;
		$this->id = isset($params[4]) ? $params[4] : '';
	}

	protected function getInput() : string
	{
		$this->options['class'] .= ' col-md-6';

		$str = '		<div class="input-group">';
		$str .= '       ' . $this->generateForm('input', $this->type, $this->name, $this->value, $this->options) . "\n";
		$str .= '		<div class="input-group-btn">';
		$str .= '    		<button type="button" class="popup_selector btn btn-default" data-inputid="' . $this->id . '">' . __('form.select_file') . '</button>';
		$str .= '		</div>';
		$str .= '		</div>';

		return $str;
	}
}
