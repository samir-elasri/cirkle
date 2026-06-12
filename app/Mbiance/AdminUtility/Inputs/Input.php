<?php

namespace Mbiance\AdminUtility\Inputs;

use Mbiance\AdminUtility\Inputs\Contracts\Inputable;

abstract class Input implements Inputable
{
	protected $type = 'text';

	protected $form;

	protected $name;

	protected $list;

	protected $value;

	protected $options;

	protected $id;

	protected $isRequired;

	public function __construct($form, $params)
	{
		$this->form = $form;
		$this->name = $params[0];
		$this->value = $params[1];
		$this->options = isset($params[2]) ? $params[2] : [];
		$this->isRequired = isset($params[3]) ? $params[3] : false;
	}

	public static function generate($form, ...$params): string
	{
		$childClass = get_called_class();
		return (new $childClass($form, $params))->getInput();
	}

	protected function getInput(): string
	{
		return $this->generateForm('input', $this->type, $this->name, $this->value, $this->options);
	}

	protected function generateForm($name, ...$params): string
	{
		return call_user_func_array([$this->form, $name], $params);
	}
}
