<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Detail Produk: ') }} <span class="font-bold">{{ $product->name }}</span>
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Dibuat oleh: {{ $product->creator->name ?? 'N/A' }} pada {{ $product->created_at->isoFormat('D MMM YYYY, HH:mm') }}</p>
                @if($product->updater)
                <p class="text-sm text-gray-500 dark:text-gray-400">Diupdate oleh: {{ $product->updater->name ?? 'N/A' }} pada {{ $product->updated_at->isoFormat('D MMM YYYY, HH:mm') }}</p>
                @endif
            </div>
            <div class="mt-3 sm:mt-0">
                <a href="{{ route('inventory.products.edit', $product->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded mr-2 text-sm">
                    Edit Produk Ini
                </a>
                 <a href="{{ route('inventory.variants.create', $product->id) }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-sm">
                    + Tambah Varian Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Deskripsi Produk:</h3>
                    <p class="text-gray-700 dark:text-gray-300 prose dark:prose-invert max-w-none">
                        {!! nl2br(e($product->description)) ?: '<span class="italic">Tidak ada deskripsi.</span>' !!}
                    </p>
                </div>
            </div>

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
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Daftar Varian Produk:</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium">Ukuran</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium">Stok</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium">Harga Beli</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium">Harga Jual</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium">Last Update By</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($product->variants->sortBy('size') as $variant) {{-- Eager load 'variants' di controller --}}
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $variant->size }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-right @if($variant->current_stock <= 5) text-red-500 font-bold @else text-gray-500 dark:text-gray-300 @endif">
                                            {{ $variant->current_stock }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-300">Rp {{ number_format($variant->purchase_price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-300">Rp {{ number_format($variant->selling_price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $variant->updater->name ?? ($variant->creator->name ?? 'N/A') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('inventory.variants.edit', $variant->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 mr-2">Edit</a>
                                            <form action="{{ route('inventory.variants.destroy', $variant->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus varian {{ $variant->size }} ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-2 text-center text-sm text-gray-500 dark:text-gray-300">Belum ada varian untuk produk ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
             <div class="mt-6 text-center">
                <a href="{{ route('inventory.products.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                    &larr; Kembali ke Daftar Produk
                </a>
            </div>
        </div>
    </div>
</x-app-layout>