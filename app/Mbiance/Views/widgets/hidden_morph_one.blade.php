<?php
	$idField = str_replace('_type', '_id', $field);
	$typeField = str_replace('_id', '_type', $field);
?>
{!! Form::hidden($idField, Request::segment(3)) !!}
{!! Form::hidden($typeField, ModelUtility::getClassNameFromRoute()) !!}