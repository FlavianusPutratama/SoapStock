<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Transaksi Penjualan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8"> {{-- Lebar agar muat banyak field --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                            <div class="font-bold">{{ __('Whoops! Ada yang salah dengan input Anda.') }}</div>
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
                                <input id="customer_name" class="block mt-1 w-full rounded-md shadow-sm" type="text" name="customer_name" value="{{ old('customer_name') }}" />
                                @error('customer_name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="sale_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Tanggal Transaksi') }}</label>
                                <input id="sale_date"
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50" {{-- Kembalikan class styling normal --}}
                                    type="date"
                                    name="sale_date"
                                    value="{{ old('sale_date', now()->format('Y-m-d')) }}"
                                    required /> {{-- Hapus atribut readonly --}}
                                @error('sale_date') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="payment_method" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Metode Pembayaran') }}</label>
                                <select id="payment_method" name="payment_method" class="block mt-1 w-full rounded-md shadow-sm" required>
                                    <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Transfer" {{ old('payment_method') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                                    <option value="QRIS" {{ old('payment_method') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                                    <option value="Lainnya" {{ old('payment_method') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('payment_method') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="payment_status" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Status Pembayaran') }}</label>
                                <select id="payment_status" name="payment_status" class="block mt-1 w-full rounded-md shadow-sm" required>
                                    <option value="Sudah Dibayar" {{ old('payment_status', 'Sudah Dibayar') == 'Sudah Dibayar' ? 'selected' : '' }}>Sudah Dibayar</option>
                                    <option value="Belum Dibayar" {{ old('payment_status') == 'Belum Dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                                </select>
                                @error('payment_status') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Item Produk</h3>
                            @error('items') <p class="mb-2 text-sm text-red-500">{{ $message }}</p> @enderror

                            <div id="sale-items-container" class="space-y-4">
                                {{-- Baris item pertama (dan seterusnya jika old input ada) --}}
                                @php $itemCount = count(old('items', [[]])); @endphp
                                @for ($i = 0; $i < $itemCount; $i++)
                                <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-3 p-3 border dark:border-gray-600 rounded-md relative">
                                    <div class="sm:col-span-6">
                                        <label for="items[{{ $i }}][product_variant_id]" class="text-xs">Produk Varian</label>
                                        <select name="items[{{ $i }}][product_variant_id]" class="product-variant-select block mt-1 w-full text-sm rounded-md shadow-sm" required data-index="{{ $i }}">
                                            <option value="">Pilih Produk...</option>
                                            @foreach ($productVariants as $variant)
                                                <option value="{{ $variant['id'] }}" data-price="{{ $variant['price'] }}" data-stock="{{ $variant['stock'] }}"
                                                        {{ old("items.{$i}.product_variant_id") == $variant['id'] ? 'selected' : '' }}>
                                                    {{ $variant['name'] }} (Stok: {{ $variant['stock'] }}) - Rp {{ number_format($variant['price'],0,',','.') }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("items.{$i}.product_variant_id") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="items[{{ $i }}][quantity_sold]" class="text-xs">Jml</label>
                                        <input type="number" name="items[{{ $i }}][quantity_sold]" value="{{ old("items.{$i}.quantity_sold", 1) }}" min="1" class="quantity-input block mt-1 w-full text-sm rounded-md shadow-sm" required data-index="{{ $i }}">
                                        @error("items.{$i}.quantity_sold") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="sm:col-span-3">
                                         <label class="text-xs">Subtotal</label>
                                         <p class="item-subtotal mt-1 p-2 block w-full text-sm rounded-md bg-gray-100 dark:bg-gray-700">Rp 0</p>
                                    </div>
                                    @if ($i > 0) {{-- Tombol hapus hanya untuk baris tambahan --}}
                                    <div class="sm:col-span-1 flex items-end">
                                        <button type="button" class="remove-item-btn text-red-500 hover:text-red-700 p-2">Hapus</button>
                                    </div>
                                    @endif
                                </div>
                                @endfor
                            </div>
                            <button type="button" id="add-item-btn" class="mt-4 px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-md">Tambah Item</button>
                        </div>

                        <div class="mt-6 pt-4 border-t dark:border-gray-700">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Total: <span id="grand-total">Rp 0</span></h3>
                        </div>

                        <div class="mt-4">
                            <label for="notes" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Catatan (Opsional)') }}</label>
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full rounded-md shadow-sm">{{ old('notes') }}</textarea>
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

    <script>
        // Basic JS untuk Tambah/Hapus Item dan Kalkulasi
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('sale-items-container');
            const addItemBtn = document.getElementById('add-item-btn');
            const grandTotalEl = document.getElementById('grand-total');
            let itemIndex = {{ $itemCount }}; // Mulai index dari jumlah item yg sudah ada (jika ada old input)

            function calculateRowSubtotal(row) {
                const variantSelect = row.querySelector('.product-variant-select');
                const quantityInput = row.querySelector('.quantity-input');
                const subtotalEl = row.querySelector('.item-subtotal');

                const selectedOption = variantSelect.options[variantSelect.selectedIndex];
                const price = parseFloat(selectedOption.dataset.price) || 0;
                const quantity = parseInt(quantityInput.value) || 0;
                const maxStock = parseInt(selectedOption.dataset.stock) || 0;

                if (quantity > maxStock && maxStock > 0) {
                    quantityInput.value = maxStock; // Batasi kuantitas dengan stok
                    // alert('Kuantitas melebihi stok yang tersedia: ' + maxStock);
                }

                const subtotal = price * (quantity > maxStock && maxStock > 0 ? maxStock : quantity) ;
                if (subtotalEl) subtotalEl.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let total = 0;
                container.querySelectorAll('.item-row').forEach(row => {
                    const variantSelect = row.querySelector('.product-variant-select');
                    const quantityInput = row.querySelector('.quantity-input');
                    const selectedOption = variantSelect.options[variantSelect.selectedIndex];
                    const price = parseFloat(selectedOption.dataset.price) || 0;
                    const quantity = parseInt(quantityInput.value) || 0;
                    const maxStock = parseInt(selectedOption.dataset.stock) || 0;
                    total += price * (quantity > maxStock && maxStock > 0 ? maxStock : quantity);
                });
                grandTotalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
            }

            function addRowListeners(row) {
                row.querySelector('.product-variant-select').addEventListener('change', function() {
                     // Saat produk dipilih, set harga jual ke input tersembunyi jika ada
                     calculateRowSubtotal(row);
                });
                row.querySelector('.quantity-input').addEventListener('input', function() {
                    calculateRowSubtotal(row);
                });
                const removeBtn = row.querySelector('.remove-item-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function () {
                        row.remove();
                        calculateGrandTotal();
                    });
                }
                calculateRowSubtotal(row); // Hitung subtotal awal
            }

            // Attach listeners to existing rows (from old input)
            container.querySelectorAll('.item-row').forEach(row => {
                addRowListeners(row);
            });
            calculateGrandTotal(); // Hitung grand total awal jika ada old input


            addItemBtn.addEventListener('click', function () {
                const firstItemRow = container.querySelector('.item-row');
                if (!firstItemRow) { // Jika tidak ada baris sama sekali (seharusnya tidak terjadi)
                    console.error('No first item row template found');
                    return;
                }
                const newItemRow = firstItemRow.cloneNode(true); // Klon baris pertama

                // Update name dan id untuk field input di baris baru
                newItemRow.querySelectorAll('select, input').forEach(input => {
                    const currentName = input.getAttribute('name');
                    if (currentName) {
                        input.setAttribute('name', currentName.replace(/items\[\d+\]/, 'items[' + itemIndex + ']'));
                    }
                    // Reset value untuk input dan select, kecuali select produk
                    if(input.type !== 'select-one' || !input.classList.contains('product-variant-select')) {
                       if (input.type === 'number') input.value = 1; else input.value = '';
                    } else {
                        input.selectedIndex = 0; // Pilih opsi "Pilih Produk..."
                    }
                    input.dataset.index = itemIndex; // Update data-index
                });

                // Pastikan tombol hapus ada dan berfungsi untuk baris baru
                let removeBtn = newItemRow.querySelector('.remove-item-btn');
                if (!removeBtn && newItemRow.querySelector('.sm\\:col-span-1')) { // Check if structure for button exists
                    const buttonContainer = newItemRow.querySelector('.sm\\:col-span-1.flex.items-end') || newItemRow.querySelector('.sm\\:col-span-1');
                    if (buttonContainer) {
                         removeBtn = document.createElement('button');
                         removeBtn.type = 'button';
                         removeBtn.classList.add('remove-item-btn', 'text-red-500', 'hover:text-red-700', 'p-2');
                         removeBtn.textContent = 'Hapus';
                         // Clear previous content if any, then append button
                         while (buttonContainer.firstChild) buttonContainer.removeChild(buttonContainer.firstChild);
                         buttonContainer.appendChild(removeBtn);
                    }
                }


                container.appendChild(newItemRow);
                addRowListeners(newItemRow); // Tambahkan event listener ke baris baru
                itemIndex++;
                calculateGrandTotal();
            });
        });
    </script>
</x-app-layout>