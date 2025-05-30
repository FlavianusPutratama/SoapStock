<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Penjual') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Selamat datang kembali, ") }} {{ auth()->user()->name }}!
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Penjualan Hari Ini
                        </h3>
                        <p class="mt-1 text-3xl font-semibold text-indigo-600 dark:text-indigo-400">
                            {{ $todaySalesCount ?? 0 }} Transaksi
                        </p>
                        <p class="mt-1 text-md text-gray-600 dark:text-gray-300">
                            Total: Rp {{ number_format($todaySalesAmount ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Pesanan Belum Dibayar
                        </h3>
                        <p class="mt-1 text-3xl font-semibold text-red-600 dark:text-red-400">
                            {{ $pendingOrdersCount ?? 0 }} Pesanan
                        </p>
                        <a href="{{ route('penjual.orders.index', ['status' => 'Belum Dibayar']) }}" class="mt-2 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                            Lihat Detail
                        </a>
                    </div>
                </div>

                @if(isset($lowStockVariants) && $lowStockVariants->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg md:col-span-2 lg:col-span-1">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Stok Segera Habis (<= 5)
                        </h3>
                        <ul class="mt-2 list-disc list-inside text-sm text-gray-700 dark:text-gray-300">
                            @foreach($lowStockVariants as $variant)
                                <li>
                                    {{ $variant->product->name }} - {{ $variant->size }}:
                                    <span class="font-semibold">{{ $variant->current_stock }}</span> unit
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>

            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Aksi Cepat:</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <a href="{{ route('penjual.sales.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded text-center">
                        Transaksi Baru
                    </a>
                    <a href="{{ route('penjual.stockins.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded text-center">
                        Input Stok Masuk
                    </a>
                    <a href="{{ route('inventory.products.create') }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-3 px-4 rounded text-center">
                        Tambah Produk Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>