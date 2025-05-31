<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Manajemen User') }}
            </h2>
            <a href="{{ route('admin.users.create') }}" class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Tambah User Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Success Alert --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90"
                     class="mb-4 flex items-start p-4 bg-green-50 dark:bg-green-800 dark:bg-opacity-30 border-l-4 border-success dark:border-green-400 text-green-700 dark:text-green-300 rounded-r-md shadow-md relative" role="alert">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div class="ml-3 flex-grow">
                        <p class="font-semibold">{{ __('Sukses!') }}</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-green-500 dark:text-green-400 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-100 dark:hover:bg-green-700 inline-flex h-8 w-8" aria-label="Dismiss">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            {{-- Error Alert --}}
            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90"
                     class="mb-4 flex items-start p-4 bg-red-50 dark:bg-red-800 dark:bg-opacity-30 border-l-4 border-danger dark:border-red-400 text-red-700 dark:text-red-300 rounded-r-md shadow-md relative" role="alert">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle fa-lg"></i>
                    </div>
                    <div class="ml-3 flex-grow">
                        <p class="font-semibold">{{ __('Error!') }}</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-red-500 dark:text-red-400 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-100 dark:hover:bg-red-700 inline-flex h-8 w-8" aria-label="Dismiss">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tgl Dibuat</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $user->role == 'superadmin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $user->created_at->format('d M Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-3">Edit</a>
                                            @if(auth()->id() != $user->id) {{-- Jangan tampilkan tombol hapus untuk diri sendiri --}}
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200">Hapus</button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">Tidak ada data user.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Jika menggunakan paginasi:
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                    --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>