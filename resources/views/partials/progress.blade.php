<div class="progress">
    @for ($i = 1; $i <= $length; $i++)
        <div class="progress__node {{ $i === $active ? 'progress__node--active' : '' }}">{{ $i }}</div>
        @if ($i < $length)
            <div class="progress__connector"></div>
        @endif
    @endfor
</div>
