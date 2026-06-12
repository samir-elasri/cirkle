<?php

namespace Mbiance\MediaUtility;

use App\Models\Core\Transliteration;
use Arr;
use File;
use Image;
use Str;

trait MediaTrait
{

	private $file;

	private $tag;

	private $fileType;

	private $locale;

	private $filename_original;

	private $filename_new;

	private $directory;

	private $directory_uri = '';

	/**
	 * Save le media sur disque ou base de données
	 *
	 * @param  mixed  $file  single ou multiple file upload
	 * @param  string  $tag
	 * @param  string  $type
	 * @param  null  $locale
	 * @return string
	 */
	public function saveMedia($file, $tag = 'default', $type = 'single', $locale = null)
	{

		$this->file = $file;
		$this->tag = $tag;
		$this->fileType = $type;
		$this->locale = $locale;
		$this->setup();

		$result = $this->getFilePath();
		$this->saveFile();

		return $result;
	}

	private function setup()
	{
		$public_path = rtrim(config('media.public_path'), '/\\') . '/';
		$files_directory = rtrim(ltrim(config('media.files_directory'), '/\\'), '/\\') . '/';
		$create_sub_directories = config('media.sub_directories');
		$this->directory = $public_path . $files_directory;
		$this->directory_uri = '';

		if ($create_sub_directories) {

			$this->directory_uri .= Str::lower(class_basename($this)) . '/' . $this->tag . '/';

			$isSegregate = false;
			if (array_key_exists($this->tag, $this->medias)) {
				$isSegregate = isset($this->medias[$this->tag]['segragate']) ? $this->medias[$this->tag]['segragate'] : false;
			}

			if (($this->fileType == 'multiple') || ($isSegregate)) {
				$this->directory_uri .= $this->id . '/';
			}
		}

		$this->directory .= $this->directory_uri;

		if ($this->file) {
			$this->filename_original = $this->file->getClientOriginalName();
			$this->filename_new = $this->getFilename();
		}
	}

	private function getFilePath()
	{

		$property = $this->tag;

		if ($this->isPropertyExists($property)) { //si la propriété existe - on retourne url

			$isRestricted = false;

			if (array_key_exists($this->tag, $this->medias)) {
				$isRestricted = isset($this->medias[$this->tag]['restricted']) ? $this->medias[$this->tag]['restricted'] : false;
			}

			if ($isRestricted) {

				$this->directory = base_path() . '/uploads/documents/';
				$fi = '/medias/documents/' . $this->filename_new;
			} else {

				$fi = '/' . config('media.files_directory') . $this->directory_uri . $this->filename_new;
			}

			return $fi;
		}

		return '';
	}

	/**
	 * Sauvegarede dans le file system
	 */
	private function saveFile()
	{

		if ($this->makeDirectory($this->directory)) {

			if (
				Str::contains($this->tag, 'image')
				&&
				!in_array($this->file->getClientOriginalExtension(), ['gif', 'svg'])
			) {
				//redimensionnement automatique des images qui sont trop grandes....
				$isDeclension = false;

				$img = Image::make($this->file->getRealPath()); //http://image-v1.intervention.io/
				$img->orientate();
				$width = $img->width();
				$height = $img->height();
				$isLandsape = $width > $height;

				if (array_key_exists($this->tag, $this->medias)) {
					$isDeclension = isset($this->medias[$this->tag]['declension']);
				}

				$maxWidth = 1920;
				$maxHeight = 1080;
				$quality = 92;

				$format = $this->medias[$this->tag]['format'] ?? [];
				$fWidth = Arr::get($format, 'width');
				$fHeigth = Arr::get($format, 'height');

				if ($fWidth && $fHeigth) {
					$img->fit($fWidth, $fHeigth, static function ($constraint) {
						/** @var Constraint $constraint */
						$constraint->upsize();
					});

				} elseif (($fWidth && $width > $fWidth) || ($fHeigth && $height > $fHeigth)) {
					$img->resize($fWidth ?: null, $fHeigth ?: null, static function ($constraint) {
						/** @var Constraint $constraint */
						$constraint->aspectRatio();
						$constraint->upsize();
					});

				} elseif (($width > $maxWidth) || ($height > $maxHeight)) {
					if ($isLandsape) {
						$img->resize($maxWidth, null, function ($constraint) {
							$constraint->aspectRatio();
							$constraint->upsize();
						});

					} else {
						//portrait
						$img->resize(null, $maxHeight, function ($constraint) {
							$constraint->aspectRatio();
							$constraint->upsize();
						});
					}
				}

				$img->save($this->directory . $this->filename_new, $quality); //Sauvegarde de l'image principale

				if ($isDeclension) {

					$declinaisons = $this->medias[$this->tag]['declension'];

					foreach ($declinaisons as $key => $declinaison) {

						$imgThumb = Image::make($this->file->getRealPath());
						$nameThumb = isset($declinaison['name']) ? $declinaison['name'] : 'thumbnails';
						$sharpenThumb = isset($declinaison['sharpen']) ? $declinaison['sharpen'] : 10;
						$widthThumb = isset($declinaison['width']) ? $declinaison['width'] : $maxWidth * 0.2;
						$heightThumb = isset($declinaison['height']) ? $declinaison['height'] : $maxHeight * 0.2;
						$qualityThumb = isset($declinaison['quality']) ? $declinaison['quality'] : 70;

						if ($this->makeDirectory($this->directory . $nameThumb)) {

							if ($isLandsape) {

								$imgThumb->resize($widthThumb, null, function ($constraint) {
									$constraint->aspectRatio();
								});
							} else { //portrait

								$imgThumb->resize(null, $heightThumb, function ($constraint) {
									$constraint->aspectRatio();
								});
							}

							$imgThumb->sharpen($sharpenThumb);
							$imgThumb->save($this->directory . $nameThumb . '/' . $this->filename_new,
								$qualityThumb); //déclinaison

						}
					}
				}
			} else {

				$this->file->move($this->directory, $this->filename_new);
			}
		}
	}

	/**
	 * Helper function to process the media filename according to the settings
	 */
	private function getFilename()
	{

		switch (config('media.rename')) {
			case 'transliterate':
				$this->filename_new = Transliteration::clean_filename($this->filename_original);
				break;
			case 'unique':
				$this->filename_new = md5(microtime() . Str::random(5)) . '.' . $this->filename_original;
				break;
			case 'nothing':
				$this->filename_new = $this->file->getClientOriginalName();
				break;
		}

		if ($this->locale) {
			$this->filename_new = $this->locale . '_' . $this->filename_new;
		}

		return $this->fileExistsRename();
	}

	private function makeDirectory($directory)
	{

		if (File::isDirectory($directory)) {
			return true;
		}

		return File::makeDirectory($directory, 0755, true);
	}

	/**
	 * Checks if a file exists and creates a new name if it does
	 */
	private function fileExistsRename()
	{
		if (File::exists($this->directory . $this->filename_new)) {
			return $this->fileRename();
		}
		return $this->filename_new;
	}

	/**
	 * Appends _X au nom du fichier si le même nom de fichier existe - repris du core code igniter
	 */
	private function fileRename()
	{

		$filename = $this->filename_new;
		$extension = '.' . File::extension($this->filename_new);
		$basename = rtrim($filename, $extension);
		$increment = 0;

		while (File::exists($this->directory . $filename)) {
			$filename = $basename . '_' . ++$increment . $extension;
		}

		return $this->filename_new = $filename;
	}

	public function saveImageBase64($base64_str, $filename, $tag = 'default')
	{

		$image = Image::make($base64_str); //Intervention - decode base64 string
		$this->tag = $tag;
		$this->filename_new = $filename . '.jpg';
		$this->setup();

		$image->save($this->directory . $this->filename_new);
		return '/' . config('media.files_directory') . $this->directory_uri . $this->filename_new;
	}

	public function removeImage($filename, $tag = 'default')
	{
		$this->tag = $tag;
		$this->setup();
		\File::delete($this->directory . $filename);
	}
}
