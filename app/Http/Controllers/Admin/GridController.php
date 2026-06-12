<?php

namespace App\Http\Controllers\Admin;

use GridUtility;
use Illuminate\Routing\Controller as BaseController;
use Request;

class GridController extends BaseController {

    protected $model;

	/*
	* Constructor
	*/
	public function __construct() {
	}

    public function queryData(): string
	{
        return GridUtility::getData(Request::get('model'), Request::get('page'), Request::get('len'), Request::get('sort'), Request::get('order'), Request::get('search'));
    }
}
