<?php

namespace App\Models\Core\Forms\Fields;

abstract class InputField
{
	public $value;
	public $errorClass;
	public $requiredString;
	protected $label;
	protected $name;
	protected $options;

	public function __construct($label = null, $name = null, $value = null, $options = [])
	{
		$this->label = $label;
		$this->name = $name;
		$this->value = $value;
		$this->options = $options;
		$this->errorClass = !empty($options['error_msg']) ? 'is-danger' : '';
		$this->requiredString = \Arr::get($options, 'required', false) ? ' *' : '';
	}

	public static function generate($label, $name, $value, $options)
	{
		$childClass = get_called_class();
		return (new $childClass($label, $name, $value, $options))->output();
	}

	public abstract function output();

	protected function errorMessage()
	{
		$errorMessage = $this->options['error_msg'];

		if (empty($errorMessage)) {
			return '';
		}

		$errorMessage = str_replace($this->name, '« '.$this->label.' »', $errorMessage);
		$errorMessage = mb_convert_encoding(implode('<br>', $errorMessage), 'UTF-8');

		return "<p id='error-".$this->name."' class='has-text-danger is-size-5'>".$errorMessage. '</p>';
	}
}
