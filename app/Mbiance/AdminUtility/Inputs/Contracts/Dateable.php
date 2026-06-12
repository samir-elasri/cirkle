<?php

namespace Mbiance\AdminUtility\Inputs\Contracts;

interface Dateable {
	public function parseDate($value) : string;
}