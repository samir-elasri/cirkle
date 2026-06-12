<!DOCTYPE html>
<html lang="fr" data-ng-app="cms" id="data-ng-app">

	<head id="Head1" runat="server">

		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="keyword" content="">

		<title>Admin - {{ setting('company_name') }}</title>
		<meta name="csrf-token" content="{{ csrf_token() }}"/>

		@yield('head')

		{!! Html::style('dist/admin/css/bootstrap.min.css') !!}
		{!! Html::style('dist/admin/css/semantic.min.css') !!}
		{!! Html::style('dist/admin/css/jquery.mmenu.css') !!}
		{!! Html::style('dist/admin/css/font-awesome.min.css') !!}
		{!! Html::style('dist/admin/css/toaster.css') !!}
		{!! Html::style('dist/admin/css/bootstrap-timepicker.min.css') !!}
		{!! Html::style('dist/admin/css/style.css') !!}
		{!! Html::style('dist/admin/css/add-ons.min.css') !!}
		{!! Html::style('dist/admin/css/select2.css') !!}
		{!! Html::style('dist/admin/css/select2-bootstrap.css') !!}
		{!! Html::style('dist/admin/plugins/chosen/css/chosen.css') !!}
		{!! Html::style('dist/admin/plugins/switch/bootstrap-switch.css' ) !!}
		{!! Html::style('dist/admin/plugins/file-input/css/fileinput.min.css') !!}
		{!! Html::style('dist/admin/plugins/jasny/css/jasny-bootstrap.css') !!}
		{!! Html::style('dist/admin/app/angular/angular-ui-tree/angular-ui-tree.min.css') !!}
		{!! Html::style('dist/admin/app/angular/angular-ui-tree/demo.css' ) !!}
		{!! Html::style('dist/admin/app/angular/xeditable/xeditable.css') !!}
		{!! Html::style('dist/admin/app/angular/angular-form-gen/angular-form-gen.css') !!}
		{!! Html::style('dist/admin/app/angular/ng-img-crop/ng-img-crop.css') !!}
		{!! Html::style('dist/admin/app/angular/select.css') !!}
		{!! Html::style('dist/admin/app/plugins/colorbox/colorbox.css') !!}
		{!! Html::style('dist/admin/app/plugins/ng-sortable.min.css') !!}
		{!! Html::style('dist/admin/app/plugins/ng-sortable.style.min.css') !!}
		<link rel="shortcut icon" href="/dist/admin/img/favicon.ico" type="image/x-icon">

	</head>

	<body>

		{!! Html::script('dist/admin/tinymce.5/tinymce.min.js') !!}

		<script type="text/javascript">
			function elFinderBrowser (callback, value, meta) {
				tinymce.activeEditor.windowManager.openUrl({
					title: 'File Manager',
					url: '/elfinder/tinymce5',
					/**
					 * On message will be triggered by the child window
					 *
					 * @param dialogApi
					 * @param details
					 * @see https://www.tiny.cloud/docs/ui-components/urldialog/#configurationoptions
					 */
					onMessage: function (dialogApi, details) {
						if (details.mceAction === 'fileSelected') {
							const file = details.data.file;

							// Make file info
							const info = file.name;

							// Provide file and text for the link dialog
							if (meta.filetype === 'file') {
								callback(file.url, {text: info, title: info});
							}

							// Provide image and alt text for the image dialog
							if (meta.filetype === 'image') {
								callback(file.url, {alt: info});
							}

							// Provide alternative source and posted for the media dialog
							if (meta.filetype === 'media') {
								callback(file.url);
							}

							dialogApi.close();
						}
					}
				});
			}

			window.addEventListener('message', function (event) {
				var data = event.data;

				// Do something with the data received here
				console.log('message received from TinyMCE', data);
			});

			{{--document.cookie = "XSRF-TOKEN={{ csrf_token() }}";--}}

			var isDebug = {{ config('app.debug') ? 'true' : 'false' }};
		</script>

		<!--[if !IE]>-->
		{!! Html::script('dist/admin/js/jquery-2.1.1.min.js') !!}
		<!--<![endif]-->

		<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<!-- pour screen reader -->
		<div class="sr-only sr-only-focusable">
			<ul id="cn-tphp">
				<li id="cn-sh-link-1"><a href="#main-content">Passer au contenu principal</a></li>
				<li id="cn-sh-link-2"><a href="#sidebar">Passer à la navigation latérale</a></li>
			</ul>
		</div>

		@yield('header')

		<div style="display: flex; flex-direction: column; min-height: 100vh">

			@yield('main-content')

			@include('partials.footer')

		</div>

		<toaster-container toastr.options='{ "closeButton": false, "debug": false, "positionClass": "toast-top-right", "onclick": null,
			"showDuration": "300", "hideDuration": "1000", "timeOut": "5000", "extendedTimeOut": "1000", "showEasing": "swing",
			"hideEasing": "linear", "showMethod": "fadeIn", "hideMethod": "fadeOut" }'>
		</toaster-container>

		<div class="clearfix"></div>

		{!! Html::script('dist/admin/js/bootstrap.min.js') !!}

		{!! Html::script('dist/admin/plugins/bigdata.js') !!}
		{!! Html::script('dist/admin/plugins/datatables/js/jquery.dataTables.js') !!}
		{!! Html::script('dist/admin/plugins/datatables/js/dataTables.bootstrap.js') !!}

		{!! Html::script('dist/admin/js/jquery.mmenu.min.js') !!}
		{!! Html::script('dist/admin/js/custom.min.js') !!}
		{!! Html::script('dist/admin/js/core.js') !!}

		{!! Html::script('dist/admin/app/plugins/colorbox/jquery.colorbox-min.js') !!}
		{!! Html::script('/packages/barryvdh/elfinder/js/standalonepopup.min.js') !!}

		{!! Html::script('dist/admin/plugins/switch/bootstrap-switch.js') !!}
		{!! Html::script('dist/admin/plugins/datepicker/js/bootstrap-datepicker.min.js') !!}
		{!! Html::script('dist/admin/plugins/datepicker/js/locales/bootstrap-datepicker.fr.js') !!}

		{!! Html::script('dist/admin/plugins/jasny/js/jasny-bootstrap.js') !!}
		{!! Html::script('dist/admin/js/pages/bootstrap-timepicker.min.js') !!}
		{!! Html::script('dist/admin/js/pages/form-elements.js') !!}
		{!! Html::script('dist/admin/js/mbiance/validation.js') !!}

		{!! Html::script('dist/admin/app/angular/angular.min.js') !!}
		{!! Html::script('dist/admin/app/angular/angular-animate.min.js') !!}
		{!! Html::script('dist/admin/app/angular/angular-sanitize.min.js') !!}
		{!! Html::script('dist/admin/app/angular/toaster.js') !!}

		{!! Html::script('dist/admin/app/angular/file-upload/ng-file-upload-shim.min.js') !!}
		{!! Html::script('dist/admin/app/angular/file-upload/ng-file-upload.min.js') !!}
		{!! Html::script('dist/admin/app/angular/ng-img-crop/ng-img-crop.js') !!}

		{!! Html::script('dist/admin/app/angular/angular-translate.min.js') !!}
		{!! Html::script('dist/admin/app/angular/angular-form-gen/angular-form-gen.js') !!}

		{!! Html::script('dist/admin/app/plugins/ng-sortable.min.js') !!}
		{!! Html::script('dist/admin/app/plugins/lodash.min.js') !!}

		{!! Html::script('dist/admin/app/angular/xeditable/xeditable.js') !!}
		{!! Html::script('dist/admin/app/angular/angular-ui-tree/angular-ui-tree.min.js') !!}

		{!! Html::script('dist/admin/js/semantic.min.js') !!}

		{!! Html::script('dist/admin/js/ui-bootstrap-tpls-0.14.3.js') !!}
		{!! Html::script('dist/admin/app/angular/select.js') !!}
		{!! Html::script('dist/admin/app/angular/angular-gm.min.js') !!}
		{!! Html::script('dist/admin/app/app.js') !!}

	</body>
</html>
