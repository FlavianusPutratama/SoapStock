<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Input Stok Masuk Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                            <p class="font-bold">Oops! Ada kesalahan:</p>
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('penjual.stockins.store') }}">
                        @csrf

                        <div class="mt-4">
                            <label for="product_variant_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Produk Varian') }}</label>
                            <select id="product_variant_id" name="product_variant_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50" required>
                                <option value="">Pilih Produk Varian...</option>
                                @foreach ($productVariants as $variant)
                                    <option value="{{ $variant['id'] }}" {{ old('product_variant_id') == $variant['id'] ? 'selected' : '' }}>
                                        {{ $variant['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_variant_id')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label for="quantity_added" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Jumlah Masuk') }}</label>
                            <input id="quantity_added" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                                   type="number" name="quantity_added" value="{{ old('quantity_added', 1) }}" min="1" required />
                            @error('quantity_added') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="purchase_price_at_entry" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Harga Beli Baru per Unit dari Supplier (Opsional)') }}</label>
                            <input id="purchase_price_at_entry" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                                   type="number" name="purchase_price_at_entry"
                                   value="{{ old('purchase_price_at_entry') }}"
                                   min="0" step="50"
                                   placeholder="Kosongkan jika harga master tidak berubah" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Jika diisi, akan mengupdate harga beli master varian ini. Jika dikosongkan, harga beli master tidak berubah dan stok masuk dicatat dengan harga master saat ini.
                            </p>
                            @error('purchase_price_at_entry') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="selling_price_set_at_entry" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Harga Jual Baru per Unit ke Customer (Opsional)') }}</label>
                            <input id="selling_price_set_at_entry" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                                   type="number" name="selling_price_set_at_entry" value="{{ old('selling_price_set_at_entry') }}"
                                   min="0" step="50" placeholder="Kosongkan jika tidak berubah" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jika diisi, akan mengupdate harga jual varian ini.</p>
                            @error('selling_price_set_at_entry') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="entry_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Tanggal Barang Masuk') }}</label>
                            <input id="entry_date" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                                   type="date" name="entry_date" value="{{ old('entry_date', now()->format('Y-m-d')) }}" required />
                            @error('entry_date') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="supplier_name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Nama Supplier (Opsional)') }}</label>
                            <input id="supplier_name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                                   type="text" name="supplier_name" value="{{ old('supplier_name') }}" />
                            @error('supplier_name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="notes" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Catatan (Opsional)') }}</label>
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">{{ old('notes') }}</textarea>
                            @error('notes') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>


                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ auth()->user()->role == 'penjual' ? route('penjual.dashboard') : route('admin.dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4 underline">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 dark:focus:ring-indigo-600 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Simpan Stok Masuk') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>