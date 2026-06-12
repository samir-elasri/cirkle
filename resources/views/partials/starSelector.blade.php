<div data-component="starSelector">
	<script type="application/json">{!! json_encode([
		'classOn' => 'far fa-star-half',
		'classOff' => 'fas fa-star-half',
	], JSON_THROW_ON_ERROR) !!}</script>
	<i data-ref="starEls"></i>
	<i data-ref="starEls"></i>
	<i data-ref="starEls"></i>
	<i data-ref="starEls"></i>
	<i data-ref="starEls"></i>
	<i data-ref="starEls"></i>
	<i data-ref="starEls"></i>
	<i data-ref="starEls"></i>
	<i data-ref="starEls"></i>
	<i data-ref="starEls"></i>
	<input type="hidden" name="{{ $name }}" data-ref="valueEl">
</div>
