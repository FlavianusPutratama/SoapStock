<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Transaksi Penjualan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-md">
                            <p class="font-bold">Oops! Ada beberapa kesalahan input:</p>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('penjual.sales.store') }}" id="saleForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="customer_name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Nama Pelanggan (Opsional)') }}</label>
                                <input id="customer_name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                                       type="text" name="customer_name" value="{{ old('customer_name') }}" />
                                @error('customer_name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="sale_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Tanggal Transaksi') }}</label>
                                <input id="sale_date" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                                       type="date" name="sale_date" value="{{ old('sale_date', now()->format('Y-m-d')) }}" required />
                                @error('sale_date') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="payment_method" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Metode Pembayaran') }}</label>
                                <select id="payment_method" name="payment_method"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                                        required>
                                    <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Transfer" {{ old('payment_method') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                                    <option value="QRIS" {{ old('payment_method') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                                    <option value="Lainnya" {{ old('payment_method') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('payment_method') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="payment_status" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Status Pembayaran') }}</label>
                                <select id="payment_status" name="payment_status"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                                        required>
                                    <option value="Sudah Dibayar" {{ old('payment_status', 'Sudah Dibayar') == 'Sudah Dibayar' ? 'selected' : '' }}>Sudah Dibayar</option>
                                    <option value="Belum Dibayar" {{ old('payment_status') == 'Belum Dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                                </select>
                                @error('payment_status') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t dark:border-gray-600">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Item Produk</h3>
                                <button type="button" id="add-item-btn" class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-md shadow-sm font-semibold">Tambah Item</button>
                            </div>
                            @error('items') <p class="mb-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                            <div id="sale-items-container" class="space-y-4">
                                @php $itemCount = count(old('items', [['product_variant_id' => '', 'quantity_sold' => 1]])); @endphp
                                @for ($i = 0; $i < $itemCount; $i++)
                                <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-x-3 gap-y-2 p-3 border dark:border-gray-700 rounded-md relative bg-gray-50 dark:bg-gray-700/30">
                                    <div class="sm:col-span-6">
                                        <label for="items-{{$i}}-product_variant_id" class="text-xs font-medium text-gray-700 dark:text-gray-300">Produk Varian</label>
                                        <select name="items[{{ $i }}][product_variant_id]" id="items-{{$i}}-product_variant_id"
                                                class="product-variant-select block mt-1 w-full text-sm rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                                                required data-index="{{ $i }}">
                                            <option value="">Pilih Produk...</option>
                                            @foreach ($productVariants ?? [] as $variant)
                                                <option value="{{ $variant['id'] }}" data-price="{{ $variant['price'] }}" data-stock="{{ $variant['stock'] }}"
                                                        {{ old("items.{$i}.product_variant_id", (isset(old('items')[$i]['product_variant_id']) ? old('items')[$i]['product_variant_id'] : null)) == $variant['id'] ? 'selected' : '' }}>
                                                    {{ $variant['name'] }} (Stok: {{ $variant['stock'] }}) - Rp {{ number_format($variant['price'],0,',','.') }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("items.{$i}.product_variant_id") <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="items-{{$i}}-quantity_sold" class="text-xs font-medium text-gray-700 dark:text-gray-300">Jml</label>
                                        <input type="number" name="items[{{ $i }}][quantity_sold]" id="items-{{$i}}-quantity_sold" value="{{ old("items.{$i}.quantity_sold", 1) }}" min="1"
                                               class="quantity-input block mt-1 w-full text-sm rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                                               required data-index="{{ $i }}">
                                        @error("items.{$i}.quantity_sold") <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                    {{-- PERUBAHAN CLASS PADA DIV SUBTOTAL DI SINI (sm:col-span-6 menjadi sm:col-span-3) --}}
                                    <div class="sm:col-span-3">
                                        <label class="text-xs font-medium text-gray-700 dark:text-gray-300">Subtotal</label>
                                        {{-- Kelas pada <p> ini sudah benar dan akan dijadikan acuan untuk template --}}
                                        <p class="item-subtotal mt-1 block w-full text-sm rounded-md shadow-sm border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 p-2">
                                            Rp 0
                                        </p>
                                    </div>
                                    <div class="sm:col-span-1 flex items-end justify-center sm:justify-end">
                                        @if ($i > 0 || (is_array(old('items')) && count(old('items')) > 1 && $i >0) || (!is_array(old('items')) && $i > 0) )
                                            <button type="button" class="remove-item-btn text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors duration-150" title="Hapus Item Ini">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            </button>
                                        @else
                                            <div class="w-8 h-8"></div> {{-- Placeholder agar layout tidak geser --}}
                                        @endif
                                    </div>
                                </div>
                                @endfor
                            </div>
                            {{-- Tombol "Tambah Item" yang ini sudah benar --}}
                             <button type="button" id="add-item-btn-bottom" class="mt-4 px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-md shadow-sm font-semibold">Tambah Item</button>
                        </div>

                        <div class="mt-6 pt-4 border-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Total Keseluruhan: <span id="grand-total" class="text-indigo-600 dark:text-indigo-400">Rp 0</span></h3>
                        </div>

                        <div class="mt-4">
                            <label for="notes" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Catatan (Opsional)') }}</label>
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">{{ old('notes') }}</textarea>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ auth()->user()->role == 'penjual' ? route('penjual.dashboard') : route('admin.dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4 underline">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest focus:outline-none">
                                {{ __('Simpan Transaksi') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Template untuk JavaScript --}}
    <template id="sale-item-row-template">
        <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-x-3 gap-y-2 p-3 border dark:border-gray-700 rounded-md relative bg-gray-50 dark:bg-gray-700/30">
            <div class="sm:col-span-6">
                <label for="items-__INDEX__-product_variant_id" class="text-xs font-medium text-gray-700 dark:text-gray-300">Produk Varian</label>
                <select name="items[__INDEX__][product_variant_id]" id="items-__INDEX__-product_variant_id"
                        class="product-variant-select block mt-1 w-full text-sm rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                        required data-index="__INDEX__">
                    <option value="">Pilih Produk...</option>
                    @foreach ($productVariants ?? [] as $variant)
                        <option value="{{ $variant['id'] }}" data-price="{{ $variant['price'] }}" data-stock="{{ $variant['stock'] }}">
                            {{ $variant['name'] }} (Stok: {{ $variant['stock'] }}) - Rp {{ number_format($variant['price'],0,',','.') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label for="items-__INDEX__-quantity_sold" class="text-xs font-medium text-gray-700 dark:text-gray-300">Jml</label>
                <input type="number" name="items[__INDEX__][quantity_sold]" id="items-__INDEX__-quantity_sold" value="1" min="1"
                       class="quantity-input block mt-1 w-full text-sm rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                       required data-index="__INDEX__">
            </div>
            {{-- PERUBAHAN PADA CLASS <p> SUBTOTAL DI SINI, MENYESUAIKAN DENGAN PHP LOOP --}}
            {{-- div pembungkusnya (sm:col-span-3) sudah benar --}}
            <div class="sm:col-span-3">
                <label class="text-xs font-medium text-gray-700 dark:text-gray-300">Subtotal</label>
                <p class="item-subtotal mt-1 block w-full text-sm rounded-md shadow-sm border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 p-2">Rp 0</p>
            </div>
            <div class="sm:col-span-1 flex items-end justify-center sm:justify-end">
                <button type="button" class="remove-item-btn text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors duration-150" title="Hapus Item Ini">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                </button>
            </div>
        </div>
    </template>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('sale-items-container');
        // Anda memiliki dua tombol "Tambah Item" dengan ID yang sama. Ini tidak ideal.
        // Saya akan asumsikan tombol pertama (di atas daftar item) adalah yang utama.
        // Atau, beri ID berbeda dan attach listener ke keduanya jika diperlukan.
        // Untuk sekarang, saya akan gunakan querySelector untuk mengambil yang pertama atau pastikan ID unik.
        const addItemBtnHeader = document.getElementById('add-item-btn'); // Tombol di atas
        const addItemBtnBottom = document.getElementById('add-item-btn-bottom'); // Tombol di bawah (ID diubah)

        const saleItemRowTemplate = document.getElementById('sale-item-row-template');
        const grandTotalEl = document.getElementById('grand-total');

        let itemIndex = container.querySelectorAll('.item-row').length;

        function calculateRowSubtotal(row) {
            const variantSelect = row.querySelector('.product-variant-select');
            const quantityInput = row.querySelector('.quantity-input');
            const subtotalEl = row.querySelector('.item-subtotal');

            const selectedOption = variantSelect.options[variantSelect.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price) || 0;
            let quantity = parseInt(quantityInput.value) || 0;
            const maxStock = parseInt(selectedOption.dataset.stock) || 0;

            // Batasi quantity agar tidak melebihi stok yang tersedia
            if (selectedOption.value && maxStock > 0 && quantity > maxStock) { // Pastikan produk dipilih dan stok ada
                quantityInput.value = maxStock;
                quantity = maxStock;
                // Anda bisa tambahkan notifikasi visual di sini jika mau, misal alert atau border merah
                // console.warn(`Kuantitas untuk ${selectedOption.text} melebihi stok. Dibatasi menjadi ${maxStock}.`);
            } else if (quantity < 1 && quantityInput.value !== "") { // Pastikan quantity tidak kurang dari 1 jika sudah diisi
                 quantityInput.value = 1;
                 quantity = 1;
            }


            const subtotal = price * quantity;
            if (subtotalEl) subtotalEl.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let total = 0;
            container.querySelectorAll('.item-row').forEach(row => {
                const variantSelect = row.querySelector('.product-variant-select');
                const quantityInput = row.querySelector('.quantity-input');
                if (variantSelect && quantityInput && variantSelect.value) { // Pastikan produk dipilih (value tidak kosong)
                    const selectedOption = variantSelect.options[variantSelect.selectedIndex];
                    const price = parseFloat(selectedOption.dataset.price) || 0;
                    let quantity = parseInt(quantityInput.value) || 0;
                    const maxStock = parseInt(selectedOption.dataset.stock) || 0;

                    if (maxStock > 0 && quantity > maxStock) {
                        quantity = maxStock;
                    }
                     if (quantity < 1) {
                        quantity = 0; // Jika kuantitas < 1, anggap 0 untuk total, meski inputnya akan diset 1 oleh calculateRowSubtotal
                    }
                    total += price * quantity;
                }
            });
            grandTotalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        function addRowListeners(row) {
            const variantSelect = row.querySelector('.product-variant-select');
            const quantityInput = row.querySelector('.quantity-input');

            if (variantSelect) {
                variantSelect.addEventListener('change', function() {
                    // Reset quantity ke 1 jika produk berubah dan sudah ada isinya, untuk memicu validasi stok ulang
                    if(quantityInput.value){
                        // quantityInput.value = 1; // Opsional: reset quantity saat produk ganti
                    }
                    calculateRowSubtotal(row);
                });
            }
            if (quantityInput) {
                quantityInput.addEventListener('input', function() {
                    calculateRowSubtotal(row);
                });
                 quantityInput.addEventListener('blur', function() { // Handle jika user mengosongkan input
                    if (quantityInput.value === "" || parseInt(quantityInput.value) < 1) {
                        quantityInput.value = 1;
                    }
                    calculateRowSubtotal(row);
                });
            }

            const removeBtn = row.querySelector('.remove-item-btn');
            if (removeBtn) {
                removeBtn.addEventListener('click', function () {
                    if (container.querySelectorAll('.item-row').length > 1) {
                        row.remove();
                        calculateGrandTotal();
                        // Re-index items (opsional, jika penamaan 'name' array penting berurutan tanpa celah)
                        // reindexItems(); 
                    } else {
                        alert('Minimal harus ada satu item produk dalam transaksi.');
                    }
                });
            }
             // Hitung subtotal untuk baris ini saat pertama kali ditambahkan atau dimuat
            if (variantSelect.value) { // Hanya hitung jika produk sudah terpilih (penting untuk old input)
                calculateRowSubtotal(row);
            }
        }
        
        // Fungsi untuk re-index (jika diperlukan)
        // function reindexItems() {
        //     itemIndex = 0;
        //     container.querySelectorAll('.item-row').forEach(row => {
        //         row.querySelectorAll('[name^="items["]').forEach(input => {
        //             const oldName = input.getAttribute('name');
        //             const newName = oldName.replace(/items\[\d+\]/, `items[${itemIndex}]`);
        //             input.setAttribute('name', newName);
        //             if (input.id) {
        //                 const oldId = input.id;
        //                 const newId = oldId.replace(/items-\d+-/, `items-${itemIndex}-`);
        //                 input.id = newId;
        //                 // Update label's 'for' attribute if exists
        //                 const label = document.querySelector(`label[for="${oldId}"]`);
        //                 if (label) label.setAttribute('for', newId);
        //             }
        //             if (input.dataset.index !== undefined) {
        //                 input.dataset.index = itemIndex;
        //             }
        //         });
        //         itemIndex++;
        //     });
        // }

        // Attach listeners to existing rows (from old input or baris awal)
        container.querySelectorAll('.item-row').forEach(row => {
            addRowListeners(row);
        });
        if (container.querySelectorAll('.item-row').length > 0) {
            calculateGrandTotal();
        }

        function addNewItem() {
            if (!saleItemRowTemplate) {
                console.error("Template 'sale-item-row-template' tidak ditemukan.");
                return;
            }
            const newRowContent = saleItemRowTemplate.innerHTML.replace(/__INDEX__/g, itemIndex);
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = newRowContent;
            const actualNewRow = tempDiv.firstElementChild;

            if (actualNewRow) {
                container.appendChild(actualNewRow);
                addRowListeners(actualNewRow);
                itemIndex++;
                // calculateGrandTotal(); // calculateGrandTotal akan dipanggil oleh addRowListeners -> calculateRowSubtotal
                // Fokus ke pilihan produk di baris baru
                const newProductSelect = actualNewRow.querySelector('.product-variant-select');
                if (newProductSelect) {
                    newProductSelect.focus();
                }
            } else {
                console.error("Gagal membuat baris item baru dari template.");
            }
        }

        if (addItemBtnHeader) {
            addItemBtnHeader.addEventListener('click', addNewItem);
        } else {
            console.error("Tombol 'add-item-btn' (header) tidak ditemukan.");
        }
        
        if (addItemBtnBottom) {
            addItemBtnBottom.addEventListener('click', addNewItem);
        } else {
            console.error("Tombol 'add-item-btn-bottom' tidak ditemukan.");
        }

    });
    </script>
</x-app-layout>