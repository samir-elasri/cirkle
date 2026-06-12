<?php

namespace App\Http\Controllers;

use App;
use Route;
use View;

class StyleguideController extends PageController {


	public function index(){

		$currentSection = Route::input('section', 'all');

		if(!config('app.debug')){
			App::abort(404);
		}

		$this->createMeta('Styleguide : ' . $currentSection);

		$sectionList = [];
		$dir    = base_path() . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, ['app', 'views', 'styleguide', 'sections']);
		$files = scandir($dir);
		foreach($files as $file){
			if(strrpos($file,'.blade.php')) array_push($sectionList, substr($file, 0, strlen($file)-10));
		}

		$params['sectionList'] = $sectionList;
		$params['currentSection'] = $currentSection;
		$params['customCode'] = 'styleguide';
		$params['content'] =  View::make('styleguide.menu')->with($params)->render();
		if($currentSection !== 'all'){
			$params['content'] .= View::make('styleguide.sections.' . $currentSection)->render();
		} else {
			foreach($sectionList as $section){
				$params['content'] .= View::make('styleguide.sections.' . $section)->render();
			}
		}

		return View::make('styleguide.pages._styleguide')->with($params)->render();
	}

}
$StyleguideController = App::make(StyleguideController::class);
