<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Varian: ') }} {{ $variant->size }} (Produk: {{ $product->name }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('inventory.variants.update', $variant->id) }}">
                        @csrf
                        @method('PUT')

                         <div>
                            <label for="size" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Ukuran Varian') }}</label>
                            <input id="size" class="block mt-1 w-full rounded-md shadow-sm" type="text" name="size" value="{{ old('size', $variant->size) }}" required />
                            @error('size') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="current_stock" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Stok Saat Ini') }}</label>
                            <input id="current_stock" class="block mt-1 w-full rounded-md shadow-sm" type="number" name="current_stock" value="{{ old('current_stock', $variant->current_stock) }}" min="0" required />
                            @error('current_stock') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="purchase_price" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Harga Beli per Unit (Modal)') }}</label>
                            <input id="purchase_price" class="block mt-1 w-full rounded-md shadow-sm" type="number" name="purchase_price" value="{{ old('purchase_price', $variant->purchase_price) }}" min="0" step="50" required />
                            @error('purchase_price') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="selling_price" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Harga Jual per Unit') }}</label>
                            <input id="selling_price" class="block mt-1 w-full rounded-md shadow-sm" type="number" name="selling_price" value="{{ old('selling_price', $variant->selling_price) }}" min="0" step="50" required />
                            @error('selling_price') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('inventory.products.show', $product->id) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4 underline">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-md font-semibold text-xs text-white uppercase">
                                {{ __('Update Varian') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>