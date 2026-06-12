
@extends('_layouts.component.grid')

@section('content')

<div class="panel-body">
    <script>
        var maxdepth = 3;
		var locales = ['{!! implode("','", $locales) !!}'];
		var collections = {!! $collections !!};
        var enumGroup = {!! $enumGroup !!};
		var pages = {!! $pages !!};
    </script>

    <script type="text/ng-template" id="nodes_renderer.html">
        <div class="tree-node">
            <div data-ng-show="item.locked == 1 || item.locked == '1'" class="pull-left" data-ng-class="item.active == '1' || item.active == 1 ? 'alert-success' : 'alert-danger'" style="padding: 11px 15px; border-right: 2px solid #d4d4d4;">
                <i class="fa fa-lock" style="color: #a6a6a3;"></i>
            </div>

            <div data-ng-hide="item.locked == 1 || item.locked == '1'" class="pull-left tree-handle" data-ng-class="item.active == '1' || item.active == 1 ? 'alert-success' : 'alert-danger'" ui-tree-handle>
                <i class="fa fa-th-list" style="color: #a6a6a3;"></i>
            </div>

            <div class="pull-left" style="padding: 10px 15px;">
				<a data-nodrag data-ng-show="item.page_id" uib-tooltip="<% infoPageContent(item.page_id) %>" tooltip-placement="top"><span class="label label-default"><i class="fa fa-paperclip"></i> </span></a>
				@foreach($locales as $locale)
					<a data-nodrag data-ng-show="showLink(item, '{{ $locale }}', false)" uib-tooltip="lien ({{ $locale }}) : <% item.{{ $locale }}.url %>" tooltip-placement="top"><span class="label label-default"><i class="fa fa fa-link"></i> {{ $locale }}</span></a>
					<a data-nodrag data-ng-show="showLink(item, '{{ $locale }}', true)" uib-tooltip="lien externe ({{ $locale }}) : <% item.{{ $locale }}.url %>" tooltip-placement="top"><span class="label label-default"><i class="fa fa-external-link"></i> {{ $locale }}</span></a>
				@endforeach
            </div>

            <div class="tree-node-content" ui-tree-handle>
                <span style="font-size: 11px;">
					<?php $sep = '' ?>
                    @foreach($locales as $locale)
                        {{$sep}}<% item.{{ $locale }}.title || 'vide' %>
                        <?php $sep = ' / ' ?>
                    @endforeach
                </span>
                <a data-ng-hide="item.locked == 1 || item.locked == '1'" class="pull-right btn btn-danger  btn-xs" data-nodrag data-ng-confirm-click="Êtes-vous certain de vouloir supprimer cet élément et ses enfants ?"  data-confirmed-click="removeElement(this)" style="margin-right: 8px;" title="Supprimer cet élément et ses enfants"><i class="fa fa-trash-o"></i></a>
                <a data-ng-hide="item.locked == 1 || item.locked == '1'" class="pull-right btn btn-success btn-xs" data-nodrag data-ng-click="addChildren(this)" style="margin-right: 8px;" title="Ajouter un enfant" data-ng-if="depth() < 4"><i class="fa fa-plus"></i></a>
                <a data-ng-hide="item.locked == 1 || item.locked == '1'" class="pull-right btn btn-primary btn-xs" data-nodrag data-ng-click="editElement(this)" style="margin-right: 8px;" title="Éditer"><i class="fa fa-pencil-square-o"></i></a>
            </div>
        </div>

        <ol ui-tree-nodes="" ng-model="item.children" ng-class="{hidden: collapsed}" data-type="children">
            <li ng-repeat="item in item.children" ui-tree-node ng-include="'nodes_renderer.html'"></li>
        </ol>
    </script>

	<div data-ng-controller="ArboCtrl" data-ng-cloak data-ui-tree="treeOptions">

        <div class="panel panel-default" data-ng-repeat="group in groups">
            <div class="panel-heading">
                <h2><% group.label || 'vide' %></h2>
	        </div>

            <div class="panel-body clearfix">
                <a data-nodrag class="btn btn-success btn-xs" data-ng-click="addElement(group)"><span class="glyphicon glyphicon-plus"></span> ajouter un élément</a>
                <ul data-ui-tree-nodes="" data-ng-model="group.items" data-id="<% group.group %>-<% group.lang %>" data-type="group" style="list-style: none;">
                    <li data-ng-repeat="item in group.items" data-ui-tree-node="data-ui-tree-node"  data-ng-include="'nodes_renderer.html'"></li>
                </ul>
            </div>
        </div>
    </div>
</div>

@stop
