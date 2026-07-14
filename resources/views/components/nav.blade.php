@php
    $links = [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Listings', 'route' => 'cars.index'],
        ['label' => 'Favorites', 'route' => 'favorites.index'],
    ];
@endphp

<header class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
    <nav
        data-nav
        class="flex items-center justify-between rounded-4xl bg-white/80 px-5 py-3.5 shadow-sm shadow-ink-900/5 ring-1 ring-ink-900/5 backdrop-blur"
    >
        {{-- Wordmark --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <span class="grid h-10 w-10 place-items-center rounded-xl bg-ink-900 text-white">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 17h14M5 17a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm18 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z" />
                    <path d="M3 17v-4.2a2 2 0 0 1 .27-1l2.1-3.6A2 2 0 0 1 7.1 7.2h9.8a2 2 0 0 1 1.73 1l2.1 3.6a2 2 0 0 1 .27 1V17" />
                </svg>
            </span>
            <span class="leading-none">
                <span class="block text-lg font-bold tracking-tight text-ink-900">Velora</span>
                <span class="block text-[11px] font-medium text-ink-300">Drive Elevated</span>
            </span>
        </a>

        {{-- Desktop links --}}
        <div class="hidden items-center gap-1 lg:flex">
            @foreach ($links as $link)
                @php $isActive = request()->routeIs($link['route']); @endphp
                <a
                    href="{{ route($link['route']) }}"
                    @class([
                        'rounded-full px-4 py-2 text-sm font-medium transition',
                        'bg-ink-50 text-ink-900' => $isActive,
                        'text-ink-500 hover:text-ink-900' => ! $isActive,
                    ])
                >
                    {{ $link['label'] }}
                    @if ($link['route'] === 'favorites.index' && $favoriteCount > 0)
                        <span class="ml-1 rounded-full bg-brand-600 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $favoriteCount }}</span>
                    @endif
                </a>
            @endforeach
            <a href="{{ route('cars.index', ['condition' => 'New']) }}" class="rounded-full px-4 py-2 text-sm font-medium text-ink-500 transition hover:text-ink-900">Services</a>
            <a href="{{ route('home') }}#about" class="rounded-full px-4 py-2 text-sm font-medium text-ink-500 transition hover:text-ink-900">About Us</a>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('cars.index') }}"
                class="hidden rounded-full bg-ink-900 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-ink-900/20 transition hover:bg-ink-800 sm:inline-block"
            >
                Get Started
            </a>

            {{-- Mobile toggle --}}
            <button
                type="button"
                data-nav-toggle
                aria-expanded="false"
                aria-controls="mobile-menu"
                class="grid h-10 w-10 place-items-center rounded-xl text-ink-900 ring-1 ring-ink-900/10 lg:hidden"
            >
                <span class="sr-only">Toggle navigation</span>
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>
        </div>
    </nav>

    {{-- Mobile menu --}}
    <div id="mobile-menu" hidden class="mt-2 rounded-3xl bg-white p-3 shadow-lg shadow-ink-900/5 ring-1 ring-ink-900/5 lg:hidden">
        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}" class="block rounded-2xl px-4 py-3 text-sm font-medium text-ink-700 hover:bg-ink-50">
                {{ $link['label'] }}
            </a>
        @endforeach
        <a href="{{ route('cars.index') }}" class="mt-1 block rounded-2xl bg-ink-900 px-4 py-3 text-center text-sm font-semibold text-white">
            Get Started
        </a>
    </div>
</header>
