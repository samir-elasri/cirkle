<?php

namespace Mbiance\AdminUtility\Inputs\Contracts;

interface Inputable {
	public static function generate($form, ...$params) : string;
}