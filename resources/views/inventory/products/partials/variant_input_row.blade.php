{{-- resources/views/inventory/products/partials/variant_input_row.blade.php --}}
<div class="variant-input-row grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-2 p-4 border border-gray-200 dark:border-gray-700 rounded-lg relative bg-gray-50 dark:bg-gray-700/30 mb-3">
    <div class="sm:col-span-3">
        <label for="variants-{{ $index }}-size" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Ukuran Varian</label>
        <input id="variants-{{ $index }}-size" type="text" name="variants[{{ $index }}][size]" value="{{ old('variants.'.$index.'.size', $variantData['size'] ?? '') }}"
               class="mt-1 block w-full text-sm rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
        @error('variants.'.$index.'.size') <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
    </div>
    <div class="sm:col-span-2">
        <label for="variants-{{ $index }}-current_stock" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Stok Awal</label>
        <input id="variants-{{ $index }}-current_stock" type="number" name="variants[{{ $index }}][current_stock]" value="{{ old('variants.'.$index.'.current_stock', $variantData['current_stock'] ?? 0) }}" min="0"
               class="mt-1 block w-full text-sm rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
        @error('variants.'.$index.'.current_stock') <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
    </div>
    <div class="sm:col-span-3">
        <label for="variants-{{ $index }}-purchase_price" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Harga Beli</label>
        <input id="variants-{{ $index }}-purchase_price" type="number" name="variants[{{ $index }}][purchase_price]" value="{{ old('variants.'.$index.'.purchase_price', $variantData['purchase_price'] ?? '') }}" min="0" step="any"
               class="mt-1 block w-full text-sm rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
        @error('variants.'.$index.'.purchase_price') <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
    </div>
    <div class="sm:col-span-3">
        <label for="variants-{{ $index }}-selling_price" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Harga Jual</label>
        <input id="variants-{{ $index }}-selling_price" type="number" name="variants[{{ $index }}][selling_price]" value="{{ old('variants.'.$index.'.selling_price', $variantData['selling_price'] ?? '') }}" min="0" step="any"
               class="mt-1 block w-full text-sm rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
        @error('variants.'.$index.'.selling_price') <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
    </div>
    <div class="sm:col-span-1 flex items-end justify-center sm:justify-end">
        {{-- Tombol hapus hanya jika ini bukan baris pertama ATAU jika ada lebih dari satu baris dari old input --}}
        {{-- Logika untuk menampilkan tombol hapus sedikit disederhanakan di sini --}}
        {{-- Jika ingin tombol hapus selalu ada kecuali untuk baris pertama jika hanya satu baris: --}}
        @if($index > 0 || (is_array(old('variants')) && count(old('variants')) > 1 && $index > 0) || (!is_array(old('variants')) && $index > 0) )
            <button type="button" class="remove-variant-btn text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors duration-150" title="Hapus Varian Ini">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        @else
             {{-- Sediakan placeholder agar layout tidak bergeser jika baris pertama tidak ada tombol hapus --}}
            <div class="w-8 h-8"></div> {{-- Sesuaikan ukuran agar sama dengan tombol --}}
        @endif
    </div>
</div>