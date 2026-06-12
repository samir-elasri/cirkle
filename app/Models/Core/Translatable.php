<?php


namespace App\Models\Core;


trait Translatable
{
	use \Astrotomic\Translatable\Translatable;

	/**
	 * Permet à FormBuilder de récupérer la valeur d'un attribute multilingue et de supporter la notation "dot".
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getFormValue($key) 
	{
		return data_get(
			$this, preg_replace('/^(' . implode('|', getLocales()) . ')\.(.+)$/', '\2:\1', $key)
		);
	}

	protected function initializeTranslatable() {
		$this->with = array_unique (array_merge ($this['with'], ['translations']));
	}
}