@props(['icon' => null, 'type' => 'button', 'disabled' => false, 'href' => null])

@php
    $commonClasses = 'inline-flex items-center justify-center px-4 py-2 bg-surface dark:bg-gray-700 border border-primary text-primary dark:text-primary-light dark:border-primary-light rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-primary-light dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge([
        'class' => $commonClasses . ($disabled ? ' opacity-25 cursor-not-allowed' : ''),
        'aria-disabled' => $disabled ? 'true' : 'false',
        'tabindex' => $disabled ? '-1' : null
        ]) }}
        @if($disabled) onclick="event.preventDefault()" @endif>

        @if ($icon)
            <i class="{{ $icon }} @if($slot->isNotEmpty()) mr-2 @endif"></i>
        @endif

        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge([
        'type' => $type,
        'class' => $commonClasses . ($disabled ? ' opacity-25' : ''),
        'disabled' => $disabled
        ]) }}>

        @if ($icon)
            <i class="{{ $icon }} @if($slot->isNotEmpty()) mr-2 @endif"></i>
        @endif

        {{ $slot }}
    </button>
@endif