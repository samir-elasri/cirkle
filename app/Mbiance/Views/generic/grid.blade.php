@extends('_layouts.component.grid')

@section('content')

	{!! GridUtility::showGrid() !!}
	@if (!empty(Session::get('grid-state')))
		<script id="grid-state" type="application/json">
			{!! json_encode(Session::get('grid-state')) !!}
		</script>
	@endif
	@include('partials.confirmModal')

	{{-- Filtre « Clients / Fournisseurs » — UNIQUEMENT sur la liste des inscrits (Denis 31.07).
	     Utilise la recherche de colonne de DataTables sur la colonne « Type » (Client/Fournisseur),
	     donc compatible avec la pagination. Ne s'active que si data-model = Subscriber. --}}
	<script>
		(function () {
			var t = document.querySelector('table.datatable');
			if (!t) return;
			if ((t.getAttribute('data-model') || '').indexOf('Subscriber') === -1) return;
			var idx = -1, ths = t.querySelectorAll('thead th');
			for (var i = 0; i < ths.length; i++) {
				if (ths[i].getAttribute('data-field') === 'member_type') { idx = i; break; }
			}
			if (idx < 0) return;

			function build() {
				if (!(window.jQuery && jQuery.fn && jQuery.fn.dataTable && jQuery.fn.dataTable.isDataTable(t))) {
					return setTimeout(build, 150);
				}
				if (t.getAttribute('data-ck-typefilter')) return;
				t.setAttribute('data-ck-typefilter', '1');
				var api = jQuery(t).DataTable();

				var bar = document.createElement('div');
				bar.style.cssText = 'margin:0 0 12px;display:flex;gap:8px;align-items:center;flex-wrap:wrap';
				var lbl = document.createElement('strong');
				lbl.textContent = 'Afficher :';
				lbl.style.marginRight = '4px';
				bar.appendChild(lbl);

				var opts = [['Tous', ''], ['Clients seulement', 'Client'], ['Fournisseurs seulement', 'Fournisseur']];
				var btns = [];
				opts.forEach(function (o) {
					var b = document.createElement('button');
					b.type = 'button';
					b.textContent = o[0];
					b.className = 'btn btn-sm btn-default';
					b.onclick = function () {
						api.column(idx).search(o[1]).draw();
						btns.forEach(function (x) { x.className = 'btn btn-sm btn-default'; });
						b.className = 'btn btn-sm btn-primary';
					};
					btns.push(b);
					bar.appendChild(b);
				});
				btns[0].className = 'btn btn-sm btn-primary';

				var wrap = t.closest('.dataTables_wrapper') || t.parentNode;
				wrap.parentNode.insertBefore(bar, wrap);
			}
			build();
		})();
	</script>

@stop
