<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-2 sm:mb-0">
                {{ __('Lacak Status Pesanan') }}
            </h2>
            {{-- Form Filter Status --}}
            <form method="GET" action="{{ route('penjual.orders.index') }}" class="flex items-center">
                <select name="status" onchange="this.form.submit()"
                        class="block w-full sm:w-auto rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 text-sm">
                    {{-- Pastikan $allowedStatuses dan $filterStatus dikirim dari controller --}}
                    @foreach($allowedStatuses as $statusOption) {{-- Hapus ?? [...] jika controller pasti mengirimkannya --}}
                        <option value="{{ $statusOption }}" {{ $filterStatus == $statusOption ? 'selected' : '' }}> {{-- Hapus ?? 'Belum Dibayar' jika controller pasti mengirimkannya --}}
                            {{ $statusOption }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            {{-- BLOK UNTUK MENAMPILKAN FLASH MESSAGE SUKSES/ERROR --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-md shadow-md" role="alert">
                    <div class="flex">
                        <div class="py-1"><svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                        <div>
                            <p class="font-bold">Sukses!</p>
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            @if (session('error'))
                 <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-md shadow-md" role="alert">
                    <div class="flex">
                        <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 5h2v6H9V5zm0 8h2v2H9v-2z"/></svg></div>
                        <div>
                            <p class="font-bold">Error!</p>
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            {{-- AKHIR BLOK FLASH MESSAGE --}}

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID Order</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tgl Transaksi</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pelanggan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item Dibeli</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total (Rp)</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Bayar</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($sales as $sale)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">SO-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $sale->sale_date->format('d M Y, H:i') }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $sale->customer_name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-normal text-sm text-gray-500 dark:text-gray-300">
                                            @if($sale->items->isNotEmpty())
                                                <ul class="list-disc list-inside space-y-1 text-xs">
                                                    @foreach($sale->items as $item)
                                                        <li>
                                                            <span class="font-medium text-gray-700 dark:text-gray-200">{{ $item->productVariant->product->name ?? 'Produk Dihapus' }}</span>
                                                            ({{ $item->productVariant->size ?? 'N/A' }})
                                                            <span class="font-semibold">x {{ $item->quantity_sold }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="italic text-xs">Tidak ada item.</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-right">{{ number_format($sale->total_amount_sold, 0, ',', '.') }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                                            <span class="px-2 py-1 inline-flex text-xs leading-tight font-semibold rounded-full
                                                @if($sale->payment_status == 'Sudah Dibayar') bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100
                                                @elseif($sale->payment_status == 'Belum Dibayar') bg-yellow-100 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100
                                                @else bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100 @endif">
                                                {{ $sale->payment_status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($sale->payment_status == 'Belum Dibayar')
                                            <form action="{{ route('penjual.orders.update_status', $sale->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="payment_status" value="Sudah Dibayar">
                                                <button type="submit" class="text-xs text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-200 px-2 py-1 rounded-md hover:bg-green-100 dark:hover:bg-gray-700" onclick="return confirm('Ubah status menjadi Sudah Dibayar?')">
                                                    Tandai Lunas
                                                </button>
                                            </form>
                                            @elseif($sale->payment_status == 'Sudah Dibayar')
                                            <form action="{{ route('penjual.orders.update_status', $sale->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="payment_status" value="Belum Dibayar">
                                                <button type="submit" class="text-xs text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-200 px-2 py-1 rounded-md hover:bg-yellow-100 dark:hover:bg-gray-700" onclick="return confirm('Ubah status menjadi Belum Dibayar?')">
                                                    Batal Lunas
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">
                                            Tidak ada pesanan dengan status "{{ $filterStatus }}".
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $sales->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>