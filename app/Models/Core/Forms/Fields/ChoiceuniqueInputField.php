<?php

namespace App\Models\Core\Forms\Fields;

use View;

class ChoiceuniqueInputField extends InputField implements Htmlable
{
	public function output() : string
	{
		return View::make('core.partials.form.choiceunique', [
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