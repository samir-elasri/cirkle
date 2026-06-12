<?php

namespace App\Models\Core\Forms\Fields;

use View;

class DateInputField extends InputField implements Htmlable
{
	public function output() : string
	{
		return View::make('core.partials.form.date', [
			'label' => $this->label,
			'requiredString' => $this->requiredString,
			'options' => $this->options,
			'value' => $this->value,
			'errorClass' => $this->errorClass,
			'name' => $this->name,
			'errorMessage' => $this->errorMessage(),
		])->render();
	}
}