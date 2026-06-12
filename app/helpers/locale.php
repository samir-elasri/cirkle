<?php

function isMultilingual()
{
	return config('app.multilingual');
}

function getLocales()
{
	if (isMultilingual()) {
		return config('translatable.locales');
	}

	return [config('app.locale')];
}
