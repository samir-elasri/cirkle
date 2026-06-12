<?php

namespace Database\Seeders;

use App\Models\Core\Bloc;
use App\Models\Core\Page;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class BlocSeeder extends Seeder
{

	public function run()
	{
		DB::table('blocs')->delete();

		// Faker
		$this->faker = Faker::create('fr_CA');

		// Page Standard
		$page = Page::getByLabel('page_standard');
		$this->getBlockBase($page, 'summary', 'Sommaire des titres des blocs');
		$this->getBlockImage($page, 'Titre bloc image #1', $this->faker->sentence(rand(3, 8)), getRandomTestImage());
		$this->getBlockImage($page, 'Titre bloc image #2', $this->faker->sentence(rand(3, 8)), getRandomTestImage());
		$this->getBlockVideoLocal($page, 'Titre video local', 'Légende', 'Description', '/tests/mp4/1.mp4', '/tests/mp4/1.jpg');
		$this->getBlockVideoYoutube($page, 'Titre de la vidéo Youtube', 'Description', 'Légende', 'AQ3hjymiCCg');
		$this->getBlockText($page, 'Titre bloc texte image droite', $this->faker->paragraph(rand(8, 15)), getRandomTestImage(), false);
		$this->getBlockText($page, 'Titre bloc texte image gauche', $this->faker->paragraph(rand(8, 15)), getRandomTestImage(), true);
		$this->getBlockText($page, 'Titre bloc avec sommaire', $this->faker->paragraph(rand(8, 15)), null, true, 'Le sommaire');
		Bloc::create([
			'page_id' => $page->id, 'type_element' => 'map', 'active' => true,
			'title' => 'Map',
			'content' => faker()->sentence(rand(8, 40)),
			'latitude' =>  45.505172, 'longitude' => -73.569329, 'zoom' => 16
		]);
		Bloc::create(['type_element' => 'newsList', 'page_id' => $page->id, 'active' => true, 'title' => 'newsList-fr']);
	}

	public function getBlockVideoYoutube($page, $title, $legend, $description, $id)
	{
		return Bloc::create([
			'page_id' 		=> $page->id,
			'type_element'	=> 'videoYoutube',
			'title' => $title,
			'legend' => $legend,
			'description' => $description,
			'filename' => $id,
			'active' 		=> true,
		]);
	}

	public function getBlockVideoLocal($page, $title, $legend, $description, $video, $image)
	{

		return Bloc::create([
			'page_id' 		=> $page->id,
			'type_element'	=> 'videoLocal',
			'title' => $title,
			'legend' => $legend,
			'description' => $description,
			'filename' => $video,
			'filename_image' => $image,
			'active' 		=> true,
		]);
	}

	public function getBlockImage($page, $title, $legend, $filename = null)
	{

		return Bloc::create([
			'page_id' 		=> $page->id,
			'type_element'	=> 'image',
			'title' => $title,
			'legend' => $legend,
			'filename_image' => $filename,
			'active' 		=> true,
		]);
	}

	public function getBlockText($page, $title, $content, $filename = '', $isLeft = true, $summary = '')
	{

		return Bloc::create([
			'page_id' 		=> $page->id,
			'type_element'	=> 'text',
			'title' => $title,
			'content' => $content,
			'summary' => $summary,
			'legend' => 'légende de l\'image',
			'filename_image' => $filename,
			'align' => ($isLeft) ? 'left' : 'right',
			'active' 		=> true,
		]);
	}

	public function getBlockBase($page, $type, $title = '', $identifier = '')
	{

		return Bloc::create([
			'page_id' 		=> $page->id,
			'type_element'	=> $type,
			'title' 		=> $title,
			'identifier'	=> $identifier,
			'active' 		=> true,
		]);
	}

	public function getBlockGallery($page, $gallery, $title = '')
	{

		Bloc::create([
			'page_id' 		=> $page->id,
			'type_element'  => 'gallery',
			'title' 		=> $title,
			'identifier'    => $gallery->id,
			'active' 		=> true
		]);
	}
}
