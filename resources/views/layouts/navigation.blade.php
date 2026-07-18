<nav x-data="{ open: false, searching: false }" class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm transition-all duration-300">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <!-- Logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold group-hover:scale-105 transition-transform duration-300 shadow-md">
                        WC
                    </div>
                    <span class="font-extrabold text-xl tracking-tight text-gray-900 group-hover:text-indigo-600 transition-colors">
                        FIFA Manager
                    </span>
                </a>
            </div>

            <!-- Navigation Links (desktop) -->
            <div class="hidden space-x-6 sm:flex items-center flex-1 mx-6">
                <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    {{ __('Home') }}
                </x-nav-link>
                <x-nav-link :href="route('tournaments.index')" :active="request()->routeIs('tournaments.*')">
                    {{ __('Tournaments') }}
                </x-nav-link>
                <x-nav-link :href="route('teams.index')" :active="request()->routeIs('teams.*')">
                    {{ __('Teams') }}
                </x-nav-link>
                <x-nav-link :href="route('players.index')" :active="request()->routeIs('players.*')">
                    {{ __('Players') }}
                </x-nav-link>
                <x-nav-link :href="route('fixtures.index')" :active="request()->routeIs('fixtures.*')">
                    {{ __('Fixtures') }}
                </x-nav-link>
                <x-nav-link :href="route('standings.index')" :active="request()->routeIs('standings.*')">
                    {{ __('Standings') }}
                </x-nav-link>
                @auth
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-nav-link>
                @endauth
            </div>

            <!-- Right side: Search + Auth -->
            <div class="hidden sm:flex sm:items-center gap-3">

                <!-- Global Search Bar -->
                <form method="GET" action="{{ route('search') }}"
                      class="flex items-center"
                      x-data="{ active: {{ request()->routeIs('search') ? 'true' : 'false' }} }">
                    <div class="relative flex items-center">
                        <input
                            id="global-search"
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search teams, players…"
                            autocomplete="off"
                            @focus="active = true"
                            @blur="if (!$el.value) active = false"
                            :class="active ? 'w-56 border-indigo-400 ring-2 ring-indigo-100' : 'w-40 border-gray-300'"
                            class="transition-all duration-300 rounded-lg border bg-gray-50 text-sm text-gray-900 placeholder-gray-400 pl-9 pr-3 py-1.5 focus:outline-none"
                        >
                        <span class="absolute left-2.5 text-gray-400 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                            </svg>
                        </span>
                    </div>
                </form>

                <!-- Auth section -->
                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()?->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-xs text-gray-400">Signed in as</p>
                            <p class="text-sm font-medium text-gray-700 truncate">{{ Auth::user()?->email }}</p>
                            <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full
                                {{ Auth::user()?->role?->role_name === 'ADMIN' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ Auth::user()?->role?->role_name ?? 'VIEWER' }}
                            </span>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="text-sm font-medium bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                        Register
                    </a>
                </div>
                @endauth
            </div>

            <!-- Hamburger (mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <!-- Mobile search -->
        <div class="px-4 pt-3 pb-2">
            <form method="GET" action="{{ route('search') }}" class="flex items-center gap-2">
                <div class="relative flex-1">
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search teams, players, coaches…"
                        class="w-full rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-900 placeholder-gray-400 pl-9 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300"
                    >
                    <span class="absolute left-2.5 top-2.5 text-gray-400 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                        </svg>
                    </span>
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                    Go
                </button>
            </form>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">Home</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tournaments.index')" :active="request()->routeIs('tournaments.*')">Tournaments</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('teams.index')" :active="request()->routeIs('teams.*')">Teams</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('players.index')" :active="request()->routeIs('players.*')">Players</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('fixtures.index')" :active="request()->routeIs('fixtures.*')">Fixtures</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('standings.index')" :active="request()->routeIs('standings.*')">Standings</x-responsive-nav-link>
            @auth
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings (auth) / Login+Register (guest) -->
        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()?->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()?->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        <div class="pt-4 pb-3 border-t border-gray-200 flex gap-3 px-4">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Login</a>
            <a href="{{ route('register') }}" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Register</a>
        </div>
        @endauth
    </div>
</nav>
