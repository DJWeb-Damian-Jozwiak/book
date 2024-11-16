<button
        type="button"
        class="btn {{ $outline ? 'btn-outline-' : 'btn-' }}{{ $type->value }} {{ $size ? 'btn-' . $size : '' }} {{ $class }}"
        {{ $disabled ? 'disabled' : '' }}
>
    {{ $slot }}
</button>