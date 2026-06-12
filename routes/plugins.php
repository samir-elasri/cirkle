<?php

Route::namespace('Barryvdh')->middleware('auth:users')->group(function () {
	Route::get('elfinder', 'Elfinder\ElfinderController@showIndex')->name('elfinder.index');
	Route::any('elfinder/connector', 'Elfinder\ElfinderController@showConnector')->name('elfinder.connector');
	Route::get('elfinder/tinymce', 'Elfinder\ElfinderController@showTinyMCE')->name('elfinder.tinymce');
	Route::get('elfinder/tinymce4', 'Elfinder\ElfinderController@showTinyMCE4')->name('elfinder.tinymce4');
	Route::get('elfinder/standalonepopup/{input_id}', 'Elfinder\ElfinderController@showPopup');
	Route::get('elfinder/filepicker/{input_id}', 'Elfinder\ElfinderController@showFilePicker ')->name('elfinder.filepicker');
	Route::get('elfinder/popup/{input_id}', 'Elfinder\ElfinderController@showPopup')->name('elfinder.popup');
	Route::get('elfinder/popup/{input_id}', 'Elfinder\ElfinderController@showPopup')->name('elfinder.popup');
	Route::get('elfinder/ckeditor', 'Elfinder\ElfinderController@showCKeditor4')->name('elfinder.ckeditor');
});
