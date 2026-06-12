<?php

namespace Database\Seeders;

use Arr;
use App\Models\Core\Forms\ChoiceGroup;
use App\Models\Core\Forms\FormGenerator;
use Illuminate\Database\Seeder;

class FormsTableSeeder extends Seeder
{

	private $locale;
	private $locales;

	public function run()
	{
		$this->locale = app()->getLocale();
		$this->locales = getLocales();

		$forms = json_decode(file_get_contents(database_path('seeders/jsons/forms.json')), true);
		foreach ($forms as $label => $fields) {
            $this->createForm($label, $fields);
        }
	}

	public function createForm($label, $fields)
	{
		$parent = FormGenerator::create([
			'label' => $label,
		]);
		foreach ($fields as $field) {
			if ($field['field_type'] == 'choice_unique' || $field['field_type'] == 'choice_multiple') {
				$field['choice_group_id'] = $this->createChoices($field);
				unset($field['choices']);
				unset($field['choices_label']);
			}

			$field = gatherTranslatables($field);

			$child = $parent->formFields()->create($field);
		}
	}

	private function createChoices($field)
	{
		$group = ChoiceGroup::create([
			'label' => $field['choices_label']
		]);

		foreach ($field['choices'] as $choice) {
			$choice = gatherTranslatables($choice);
			$child = $group->choices()->create($choice);
		}

		return $group->id;
	}
}
