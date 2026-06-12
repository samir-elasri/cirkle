@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
<section>
	<div class="optimal-content-width search-content">
		<div class="content-writable">
			<div class="msg-info">
				<span class="title"><i class="icon-info"></i>
					{!! trans_choice('main.search.totalResults',$totalResultCount,['count'=>$totalResultCount,'query'=>htmlentities($query)]) !!}
				</span>
			</div>
			@foreach($results as $key => $rslt)
				<div class="searchbox">
					<h2>{{ __('main.search.'.$key) }} <small>({{ count($rslt) }})</small></h2>
					<ul class="search-results">
						@foreach($rslt as $k => $r)
							<li >
								@if(!empty($r->url))
									<a href="{{ $r->url }}">{{ $r->label }}</a>
								@else
									{{ $r->label }}
								@endif
							</li>
						@endforeach
					</ul>
				</div>
			@endforeach
		</div>
	</div>
</section>
