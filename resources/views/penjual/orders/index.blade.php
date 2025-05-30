<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-2 sm:mb-0">
                {{ __('Lacak Status Pesanan') }}
            </h2>
            {{-- Form Filter Status --}}
            <form method="GET" action="{{ route('penjual.orders.index') }}" class="flex items-center">
                <select name="status" onchange="this.form.submit()"
                        class="block w-full sm:w-auto rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                    @foreach($allowedStatuses as $statusOption)
                        <option value="{{ $statusOption }}" {{ $filterStatus == $statusOption ? 'selected' : '' }}>
                            {{ $statusOption }}
                        </option>
                    @endforeach
                </select>
                {{-- Tombol submit bisa dihilangkan jika menggunakan onchange --}}
                {{-- <button type="submit" class="ml-2 px-3 py-2 bg-indigo-600 text-white rounded-md text-sm">Filter</button> --}}
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
             @if (session('error'))
                 <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID Order</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tgl Transaksi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pelanggan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Bayar</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($sales as $sale)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">SO-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $sale->sale_date->format('d M Y, H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $sale->customer_name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">Rp {{ number_format($sale->total_amount_sold, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($sale->payment_status == 'Sudah Dibayar') bg-green-100 text-green-800 @elseif($sale->payment_status == 'Belum Dibayar') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                                                {{ $sale->payment_status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- Form kecil untuk update status --}}
                                            @if($sale->payment_status == 'Belum Dibayar')
                                            <form action="{{ route('penjual.orders.update_status', $sale->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="payment_status" value="Sudah Dibayar">
                                                <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-200" onclick="return confirm('Ubah status menjadi Sudah Dibayar?')">
                                                    Tandai Lunas
                                                </button>
                                            </form>
                                            @elseif($sale->payment_status == 'Sudah Dibayar')
                                            <form action="{{ route('penjual.orders.update_status', $sale->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="payment_status" value="Belum Dibayar">
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-200" onclick="return confirm('Ubah status menjadi Belum Dibayar?')">
                                                    Batal Lunas
                                                </button>
                                            </form>
                                            @endif
                                            {{-- Link ke detail jika ada --}}
                                            {{-- <a href="#" class="ml-2 text-indigo-600 hover:text-indigo-900">Detail</a> --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">
                                            Tidak ada pesanan dengan status "{{ $filterStatus }}".
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $sales->appends(['status' => $filterStatus])->links() }} {{-- Menambahkan parameter filter ke link paginasi --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>