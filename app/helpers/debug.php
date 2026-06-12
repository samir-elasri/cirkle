<?php

use Faker\Factory as Faker;

$GLOBALS['faker'] = Faker::create('fr_CA');

function faker()
{
    return $GLOBALS['faker'];
}

function d($value)
{
    dump($value);
}

function eLog($message){
    $_FILE = storage_path() . '/logs/eLog.log';
    if(!isset( $GLOBALS['eLogStart'])){
        $GLOBALS['eLogStart'] = microtime(true);
        error_log('----------------[first call]--------------------' . PHP_EOL, 3, $_FILE);
    }
    $diffTime = microtime(true) - $GLOBALS['eLogStart'];
    error_log($message . ' ['. $diffTime .']' . PHP_EOL, 3, $_FILE);
}

function getRandomTestImage(){
    return '/tests/jpg/'.random_int(1,12).'.jpg';
}

function getRandomTestLogo(){
    return '/tests/logos/'.random_int(1,16).'.jpg';
}
