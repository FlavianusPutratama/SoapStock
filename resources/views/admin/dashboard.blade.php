<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Super Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Selamat datang, Super Admin ") }} {{ auth()->user()->name }}!
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 shadow rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Total Pengguna</h3>
                    <p class="mt-1 text-3xl font-semibold text-indigo-600 dark:text-indigo-400">{{ $totalUsers ?? 0 }}</p>
                    <a href="{{ route('admin.users.index') }}" class="mt-2 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Kelola Pengguna</a>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 shadow rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Total Produk</h3>
                    <p class="mt-1 text-3xl font-semibold text-indigo-600 dark:text-indigo-400">{{ $totalProducts ?? 0 }}</p>
                     <a href="{{ route('inventory.products.index') }}" class="mt-2 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Kelola Produk</a>
                </div>
                 <div class="bg-white dark:bg-gray-800 p-6 shadow rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Penjualan Hari Ini (Global)</h3>
                    <p class="mt-1 text-3xl font-semibold text-green-600 dark:text-green-400">Rp {{ number_format($todaySales ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 shadow rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Penjualan Bulan Ini (Global)</h3>
                    <p class="mt-1 text-3xl font-semibold text-green-600 dark:text-green-400">Rp {{ number_format($monthlySales ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 shadow rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pesanan Belum Dibayar (Global)</h3>
                    <p class="mt-1 text-3xl font-semibold text-red-600 dark:text-red-400">{{ $totalPendingOrders ?? 0 }}</p>
                    <a href="{{ route('penjual.orders.index', ['status' => 'Belum Dibayar']) }}" class="mt-2 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Lihat Detail</a>
                </div>
                {{-- Tambahkan card lain jika perlu --}}
            </div>
        </div>
    </div>
</x-app-layout>