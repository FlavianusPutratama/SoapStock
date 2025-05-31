<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Produk Baru beserta Variannya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8"> {{-- Max width agar form tidak terlalu lebar --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    {{-- Menampilkan error validasi umum jika ada --}}
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-md">
                            <div class="font-bold">Oops! Ada beberapa kesalahan input:</div>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('inventory.products.store') }}">
                        @csrf

                        {{-- Detail Produk Utama --}}
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3 border-b pb-2 dark:border-gray-600">Detail Produk Utama</h3>
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Nama Produk') }}</label>
                            <input id="name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                                   type="text" name="name" value="{{ old('name') }}" required autofocus />
                            @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Deskripsi (Opsional)') }}</label>
                            <textarea id="description" name="description" rows="3"
                                      class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">{{ old('description') }}</textarea>
                            @error('description') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Bagian Varian Produk --}}
                        <div class="mt-8 pt-4 border-t dark:border-gray-600">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Varian Produk</h3>
                                <button type="button" id="add-variant-btn" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-md shadow-sm">
                                    + Tambah Baris Varian
                                </button>
                            </div>
                            <div id="variants-container" class="space-y-4">
                                {{-- Loop old('variants') jika ada error validasi dan data lama --}}
                                @if(is_array(old('variants')))
                                    @foreach(old('variants') as $key => $oldVariantData)
                                        @include('inventory.products.partials.variant_input_row', ['index' => $key, 'variantData' => $oldVariantData])
                                    @endforeach
                                @else
                                    {{-- Tambahkan satu baris default jika tidak ada old input atau jika old('variants') bukan array --}}
                                     @include('inventory.products.partials.variant_input_row', ['index' => 0, 'variantData' => null])
                                @endif
                            </div>
                             @error('variants') {{-- Error umum untuk array variants, misal jika kosong tapi wajib --}}
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="flex items-center justify-end mt-8 pt-6 border-t dark:border-gray-600">
                            <a href="{{ route('inventory.products.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4 underline">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 dark:focus:ring-indigo-600 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Simpan Produk & Semua Varian') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Template untuk baris input varian baru (disembunyikan, untuk dikloning JS) --}}
    <template id="variant-row-template">
        @include('inventory.products.partials.variant_input_row', ['index' => '__INDEX__', 'variantData' => null])
    </template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const variantsContainer = document.getElementById('variants-container');
    const addVariantBtn = document.getElementById('add-variant-btn');
    const variantRowTemplate = document.getElementById('variant-row-template');

    // Inisialisasi variantIndex berdasarkan jumlah baris yang sudah ada
    let variantIndex = variantsContainer.querySelectorAll('.variant-input-row').length;

    if (addVariantBtn && variantRowTemplate) {
        addVariantBtn.addEventListener('click', function () {
            // Ambil konten dari template
            const newRowContent = variantRowTemplate.innerHTML.replace(/__INDEX__/g, variantIndex);
            // Buat elemen div sementara untuk parsing HTML string menjadi node DOM
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = newRowContent;
            // Ambil elemen .variant-input-row yang sebenarnya dari div sementara
            const actualNewRow = tempDiv.firstElementChild;

            if (actualNewRow) {
                variantsContainer.appendChild(actualNewRow);
                initializeRemoveButton(actualNewRow); // Pasang event listener ke tombol hapus di baris baru
                variantIndex++; // Naikkan index untuk baris berikutnya
            } else {
                console.error("Gagal membuat baris varian baru dari template.");
            }
        });
    } else {
        if (!addVariantBtn) console.error("Elemen dengan ID 'add-variant-btn' tidak ditemukan.");
        if (!variantRowTemplate) console.error("Elemen template dengan ID 'variant-row-template' tidak ditemukan.");
    }

    function initializeRemoveButton(row) {
        const removeBtn = row.querySelector('.remove-variant-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                // Hanya izinkan hapus jika ada lebih dari satu baris varian
                if (variantsContainer.querySelectorAll('.variant-input-row').length > 1) {
                    row.remove();
                } else {
                    alert('Minimal harus ada satu baris varian.');
                    // Opsional: kosongkan field di baris pertama jika tidak boleh dihapus
                    // row.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => input.value = '');
                    // const stockInput = row.querySelector('input[name*="[current_stock]"]');
                    // if(stockInput) stockInput.value = 0;
                }
            });
        }
    }

    // Inisialisasi tombol hapus untuk baris yang mungkin sudah ada saat halaman dimuat (dari old input)
    variantsContainer.querySelectorAll('.variant-input-row').forEach(row => {
        initializeRemoveButton(row);
    });
});
</script>
</x-app-layout>