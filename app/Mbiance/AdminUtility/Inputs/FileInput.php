<?php

namespace Mbiance\AdminUtility\Inputs;

use Html;
use Illuminate\Support\Str;

class FileInput extends Input
{
	/**
	 * @var mixed|string
	 */
	public $title;

	/**
	 * @var mixed
	 */
	public $fieldName;

	/**
	 * @var mixed|string
	 */
	public $locale;

	public function __construct($form, $params)
	{
		$this->form = $form;
		$this->fieldName = $params[0];
		$this->name = $params[1];
		$this->title = $params[2] ?? '';
		$this->locale = $params[3] ?? '';
		$this->isRequired = $params[4] ?? false;
	}

	protected function getInput(): string
	{
		$str = '';
		if (!empty($this->name)) {
			if (
				Str::endsWith($this->fieldName, 'document')
				||
				Str::endsWith($this->fieldName, 'document]')
				||
				Str::endsWith($this->fieldName, 'file')
				||
				Str::endsWith($this->fieldName, 'file]')
			) {
				$str .= '<a href="' . $this->name . '" target="_blank">' . $this->name . "</a>\n";

				if (!$this->isRequired) {
					$str .= '<span style="margin-left: 15px;">'
						. $this->form->checkbox($this->locale ? str_replace(']', '-remove]',
							$this->fieldName) : $this->fieldName . '-remove', 1)
						. ' retirer le document</span>';
				}

				$str .= '<br><br>';
			} elseif (
				Str::endsWith($this->fieldName, 'image')
				||
				Str::endsWith($this->fieldName, 'image]')
				||
				Str::endsWith($this->fieldName, 'photo')
				||
				Str::endsWith($this->fieldName, 'photo]')
			) {
				$str = '		 ' . Html::image($this->name, $this->title, array('width' => '50'));

				if (!$this->isRequired) {
					$str .= '<span style="margin-left: 15px;">'
						. $this->form->checkbox($this->locale ? str_replace(']', '-remove]',
							$this->fieldName) : $this->fieldName . '-remove', 1)
						. " retirer l'image</span>";
				}

				$str .= '<br><br>';
			}
		}
		$str .= '       ' . $this->generateForm('file', $this->fieldName) . "\n";

		return $str;
	}
}
