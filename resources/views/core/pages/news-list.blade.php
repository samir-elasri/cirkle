{!! $blocs !!}

@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
<section>
	<div class="news-list optimal-content-width">
		<div class="filters">
		{!! Form::open(['url' => urlRouteName('news-list'), 'method' => 'get']) !!}
		<div style="float: right;">
			<div class="ui selection dropdown" data-component="dropdown">
				<input type="hidden" name="category">
				<i class="dropdown icon"></i>
				<div class="default text">@lang('main.category')</div>
				<div class="menu">
					@foreach ($categories as $category)
						<div class="item" data-value="{{ $category->id }}">{{ $category->translation->title }}</div>
					@endforeach
				</div>
			</div>
			{!! Form::submit(__('main.submit'), ['class' => 'call-to-action ']) !!}
		</div>
		{!! Form::close() !!}
		</div>

		<div data-component="tileGrid" data-tile-grid-width="275" class="news-list__body">
			@each('core.partials.news-single', $news, 'singleNews')
		</div>
		{{ $news->links() }}
	</div>
</section>
