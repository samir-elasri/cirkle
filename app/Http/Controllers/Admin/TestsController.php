<?php

namespace App\Http\Controllers\Admin;

use Cache;
use Error;
use Exception;
use Redirect;
use ReflectionMethod;
use Request;
use URL;
use Validator;
use View;

class TestsController extends AdminBaseController {

    public function modelsTests() {

        /*$output = shell_exec(escapeshellcmd('ls -a'));
        $output = str_replace(['[33m', '[32m', '[39m'], ['<b style="color:blue;">','<b style="color:green;">','</b>'], $output);
        $content = '<pre>'.$output.'</pre>';*/

        $content = '';
        $allModels = [];
        $errorCount = 0;
        $methodsToCall = ['get', 'getAllMenu']; // static method only

        Cache::flush();

        // Finding valid models
        try {
            $files = glob(app_path() . DIRECTORY_SEPARATOR . 'models' .  DIRECTORY_SEPARATOR . '*.php');

            $modelsFile = array_map(function($value){
                $ex = explode(DIRECTORY_SEPARATOR, $value);
                $filename = array_pop($ex);
                $classname = substr($filename, 0, -4);
                return $classname;
            }, $files);

            $models = array_filter($modelsFile, function($value){
                return class_exists($value);
            });

        } catch (Exception|Error) {
            $content .= 'Error looping in all classes';
            return View::make('_admin.default', compact('content'))->render();
        }

        // Looping in methods and in models
        $total_start_time = microtime(TRUE);
        foreach($models as $model) {
            $modelData = (object)['title'=> $model, 'methods' => []];

            foreach($methodsToCall as $method) {
                try {
                    if(method_exists($model, $method)){
                        $refl = new ReflectionMethod($model, $method);
                        if($refl->isStatic()){ // only check static methods
                            $start_time = microtime(TRUE);
                            $data = call_user_func($model . '::' . $method);
                            if(is_object($data) && method_exists($data, 'toArray')){
                                $data = $data->toArray(); // this needed to be called for correct benchmarking
                            }
                            if(is_array($data)){
                                array_push($modelData->methods, (object)['title'=>$method, 'data'=>isset($data[0]) ? $data[0] : $data, 'objCount'=> count($data), 'benchmark'=>  (microtime(TRUE) - $start_time), 'success'=>true]);
                            }
                        }
                    }
                } catch (Exception|Error) {
                    $errorCount++;
                    array_push($modelData->methods, (object)['title'=>$method, 'objCount'=> 0, 'benchmark'=>  (microtime(TRUE) - $start_time), 'success'=>false]);
                }
            }
            array_push($allModels, $modelData);
        }

        $totalBenchmark = (microtime(TRUE) - $total_start_time);

        $content .= View::make('_admin.widgets.models-test', compact('allModels', 'totalBenchmark', 'errorCount'))->render();
        return View::make('_admin.default', compact('content'))->render();
    }


    public function formTest() {
        $FORM_NAME = 'demoForm';

        $rules = ['texte' => 'required|string', 'nombre' => 'required|numeric|min:1|max:99'];

        $validator = Validator::make(Request::all(), $rules);
        if($validator->fails())
            return Redirect::to(URL::previous() . '#' . $FORM_NAME)->withErrors($validator, $FORM_NAME)->withInput();
        else {
            return Redirect::to(URL::previous() . '#' . $FORM_NAME)->with($FORM_NAME . '_success', 'Okay, bravo !!!');
        }
    }
}
