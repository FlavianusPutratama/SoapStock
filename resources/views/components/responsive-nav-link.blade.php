@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-primary dark:border-primary-light text-left text-base font-medium text-primary-dark dark:text-gray-100 bg-primary-light dark:bg-primary-dark dark:bg-opacity-20 focus:outline-none focus:text-primary-dark dark:focus:text-gray-50 focus:bg-blue-100 dark:focus:bg-primary-dark focus:border-primary-dark dark:focus:border-gray-50 transition duration-150 ease-in-out'
            : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-text-muted dark:text-gray-400 hover:text-text-main dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-text-main dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>