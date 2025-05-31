@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge([
    'class' => 'w-full border-border-light dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary dark:focus:border-primary focus:ring-primary dark:focus:ring-primary rounded-md shadow-sm text-sm text-text-main placeholder-text-muted'
    ]) }}>