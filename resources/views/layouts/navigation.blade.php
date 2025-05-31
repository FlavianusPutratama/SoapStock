<nav x-data="{ open: false }" class="bg-surface dark:bg-gray-800 border-b border-border-light dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-primary-dark dark:text-gray-200" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('penjual.dashboard')">
                        <i class="fas fa-tachometer-alt mr-2"></i>{{ __('Dashboard') }}
                    </x-nav-link>

                    @auth
                        @if(auth()->user()->role == 'superadmin')
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                <i class="fas fa-users-cog mr-2"></i>{{ __('Manajemen User') }}
                            </x-nav-link>
                            <x-nav-link :href="route('inventory.products.index')" :active="request()->routeIs('inventory.products.*') || request()->routeIs('inventory.variants.*')">
                                <i class="fas fa-boxes mr-2"></i>{{ __('Manajemen Produk') }}
                            </x-nav-link>
                            <x-nav-link :href="route('penjual.orders.index')" :active="request()->routeIs('penjual.orders.*')">
                                <i class="fas fa-receipt mr-2"></i>{{ __('Lacak Pesanan') }}
                            </x-nav-link>
                            <x-nav-link :href="route('penjual.revenue.report')" :active="request()->routeIs('penjual.revenue.report')">
                                <i class="fas fa-chart-line mr-2"></i>{{ __('Laporan Revenue') }}
                            </x-nav-link>
                        @elseif(auth()->user()->role == 'penjual')
                            <x-nav-link :href="route('penjual.sales.create')" :active="request()->routeIs('penjual.sales.create')">
                                <i class="fas fa-cash-register mr-2"></i>{{ __('Transaksi Baru') }}
                            </x-nav-link>
                            <x-nav-link :href="route('penjual.stockins.create')" :active="request()->routeIs('penjual.stockins.create')">
                                <i class="fas fa-dolly-flatbed mr-2"></i>{{ __('Stok Masuk') }}
                            </x-nav-link>
                            <x-nav-link :href="route('inventory.products.index')" :active="request()->routeIs('inventory.products.*') || request()->routeIs('inventory.variants.*')">
                                <i class="fas fa-boxes mr-2"></i>{{ __('Manajemen Produk') }}
                            </x-nav-link>
                            <x-nav-link :href="route('penjual.orders.index')" :active="request()->routeIs('penjual.orders.*')">
                                <i class="fas fa-receipt mr-2"></i>{{ __('Lacak Pesanan') }}
                            </x-nav-link>
                            <x-nav-link :href="route('penjual.revenue.report')" :active="request()->routeIs('penjual.revenue.report')">
                                <i class="fas fa-chart-line mr-2"></i>{{ __('Laporan Revenue') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-text-muted dark:text-gray-400 bg-surface dark:bg-gray-800 hover:text-text-main dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div><i class="fas fa-user-circle mr-1"></i>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                           <i class="fas fa-user-edit w-4 mr-2"></i> {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <i class="fas fa-sign-out-alt w-4 mr-2"></i>{{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                    <x-nav-link :href="route('login')" :active="request()->routeIs('login')">
                        {{ __('Log in') }}
                    </x-nav-link>
                    @if (Route::has('register'))
                        <x-nav-link :href="route('register')" :active="request()->routeIs('register')" class="ml-4">
                             {{ __('Register') }}
                        </x-nav-link>
                    @endif
                @endauth
            </div>

            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-text-muted dark:text-gray-500 hover:text-text-main dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-text-main dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('penjual.dashboard')">
                <i class="fas fa-tachometer-alt mr-2"></i>{{ __('Dashboard') }}
            </x-responsive-nav-link>
             @auth
                @if(auth()->user()->role == 'superadmin')
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        <i class="fas fa-users-cog mr-2"></i>{{ __('Manajemen User') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('inventory.products.index')" :active="request()->routeIs('inventory.products.*') || request()->routeIs('inventory.variants.*')">
                        <i class="fas fa-boxes mr-2"></i>{{ __('Manajemen Produk') }}
                    </x-responsive-nav-link>
                     <x-responsive-nav-link :href="route('penjual.orders.index')" :active="request()->routeIs('penjual.orders.*')">
                        <i class="fas fa-receipt mr-2"></i>{{ __('Lacak Pesanan') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('penjual.revenue.report')" :active="request()->routeIs('penjual.revenue.report')">
                        <i class="fas fa-chart-line mr-2"></i>{{ __('Laporan Revenue') }}
                    </x-responsive-nav-link>
                @elseif(auth()->user()->role == 'penjual')
                    <x-responsive-nav-link :href="route('penjual.sales.create')" :active="request()->routeIs('penjual.sales.create')">
                        <i class="fas fa-cash-register mr-2"></i>{{ __('Transaksi Baru') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('penjual.stockins.create')" :active="request()->routeIs('penjual.stockins.create')">
                        <i class="fas fa-dolly-flatbed mr-2"></i>{{ __('Stok Masuk') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('inventory.products.index')" :active="request()->routeIs('inventory.products.*') || request()->routeIs('inventory.variants.*')">
                        <i class="fas fa-boxes mr-2"></i>{{ __('Manajemen Produk') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('penjual.orders.index')" :active="request()->routeIs('penjual.orders.*')">
                        <i class="fas fa-receipt mr-2"></i>{{ __('Lacak Pesanan') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('penjual.revenue.report')" :active="request()->routeIs('penjual.revenue.report')">
                        <i class="fas fa-chart-line mr-2"></i>{{ __('Laporan Revenue') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        @auth
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-text-main dark:text-gray-200"><i class="fas fa-user-circle mr-1"></i>{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-text-muted dark:text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                   <i class="fas fa-user-edit w-4 mr-2"></i> {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt w-4 mr-2"></i>{{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
            <div class="py-1 border-t border-border-light dark:border-gray-600">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Log In') }}
                </x-responsive-nav-link>
                @if (Route::has('register'))
                    <x-responsive-nav-link :href="route('register')">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                @endif
            </div>
        @endauth
    </div>
</nav>