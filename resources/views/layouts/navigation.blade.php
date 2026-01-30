<nav x-data="{ open: false }" class="bg-white border-b border-brand-100 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <div class="w-8 h-8 bg-brand-500 rounded-lg flex items-center justify-center text-white font-bold text-lg shadow-lg group-hover:bg-brand-600 transition">
                            B
                        </div>
                        <span class="font-bold text-xl tracking-tight text-brand-900 group-hover:text-black transition">
                            Bizniz.IO
                        </span>
                    </a>
                </div>

                <div class="hidden space-x-6 sm:-my-px sm:ml-10 sm:flex overflow-x-auto">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @role('Staff|Owner|Admin')
                    <x-nav-link :href="route('pos.index')" :active="request()->routeIs('pos.*')">
                        {{ __('POS') }}
                    </x-nav-link>
                    <x-nav-link :href="route('warehouse.index')" :active="request()->routeIs('warehouse.*')">
                        {{ __('Gudang') }}
                    </x-nav-link>
                    <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                        {{ __('Pelanggan') }}
                    </x-nav-link>
                    @endrole

                    @role('Viewer|Owner|Admin')
                    <x-nav-link :href="route('analytics')" :active="request()->routeIs('analytics')">
                        {{ __('Analitik') }}
                    </x-nav-link>
                    @endrole

                    @role('Owner')
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        {{ __('Staf') }}
                    </x-nav-link>
                    @endrole

                    @role('Owner|Admin')
                    <x-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">
                        {{ __('Pengaturan') }}
                    </x-nav-link>
                    @endrole


                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-brand-600 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex flex-col items-end">
                                <span class="font-bold text-gray-800">{{ Auth::user()->name }}</span>
                                <span class="text-xs text-brand-500">{{ Auth::user()->getRoleNames()->first() }}</span>
                            </div>

                            <div class="ml-2 bg-brand-50 p-1 rounded-full">
                                <svg class="fill-current h-5 w-5 text-brand-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                             onclick="event.preventDefault();
                                                this.closest('form').submit();" class="text-red-600 hover:bg-red-50">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-brand-400 hover:text-brand-500 hover:bg-brand-50 focus:outline-none focus:bg-brand-50 focus:text-brand-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-100 shadow-lg">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @role('Staff|Owner|Admin')
            <x-responsive-nav-link :href="route('pos.index')" :active="request()->routeIs('pos.*')">
                {{ __('POS') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('warehouse.index')" :active="request()->routeIs('warehouse.*')">
                {{ __('Gudang') }}
            </x-responsive-nav-link>
            @endrole

            @role('Viewer|Owner|Admin')
            <x-responsive-nav-link :href="route('analytics')" :active="request()->routeIs('analytics')">
                {{ __('Analitik') }}
            </x-responsive-nav-link>
            @endrole
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 bg-brand-50">
            <div class="px-4">
                <div class="font-medium text-base text-brand-900">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-brand-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                                           onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-red-600">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
