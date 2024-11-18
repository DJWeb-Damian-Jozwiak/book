<button
        type="button"
        class="btn {{ $outline ? 'btn-outline-' : 'btn-' }}{{ $type }} {{ $size ? 'btn-' . $size : '' }} {{ $class }}"
        {{ $disabled ? 'disabled' : '' }}
>
    {{ $slot }}

            @isset($slots['badge'])
                <span class="badge">
                    {!! $slots['badge']  !!}
                </span>
            @endif

        @stack('styles')
</button>