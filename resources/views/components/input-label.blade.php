@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-text-main dark:text-gray-300']) }}>
    {{ $value ?? $slot }}
</label>