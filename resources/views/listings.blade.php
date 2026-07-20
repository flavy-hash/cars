@php
    $sidebarFilters = [
        'brand' => ['label' => 'Brand', 'options' => $filters['brands']],
        'body_type' => ['label' => 'Body type', 'options' => $filters['body_types']],
        'condition' => ['label' => 'Condition', 'options' => $filters['conditions']],
        'transmission' => ['label' => 'Transmission', 'options' => $filters['transmissions']],
        'fuel_type' => ['label' => 'Fuel', 'options' => $filters['fuel_types']],
        'location' => ['label' => 'City', 'options' => $filters['locations']],
    ];

    $sortOptions = [
        '' => 'Recommended',
        'price_asc' => 'Price: low to high',
        'price_desc' => 'Price: high to low',
        'mileage_asc' => 'Lowest mileage',
        'year_desc' => 'Newest year',
    ];
@endphp

<x-layout title="Listings">
    <section class="mx-auto max-w-7xl px-4 pt-12 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-flame">Inventory</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight text-bone">Find your next car</h1>
            <p class="mt-3 text-sm leading-relaxed text-ash">
                {{ $cars->total() }} {{ Str::plural('car', $cars->total()) }} available right now, every one inspected and history-checked.
            </p>
        </div>

        <div class="mt-8">
            <x-search-bar :filters="$filters" :active="$active" />
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[17rem_1fr]">
            {{-- ── Sidebar ── --}}
            <aside class="lg:sticky lg:top-8 lg:self-start">
                <form method="GET" action="{{ route('cars.index') }}" class="rounded-3xl bg-ink-2 p-6 shadow-sm shadow-black/20 ring-1 ring-line">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-bone">Filters</h2>
                        @if ($active)
                            <a href="{{ route('cars.index') }}" class="text-xs font-medium text-flame hover:underline">Clear all</a>
                        @endif
                    </div>

                    <label class="mt-5 block">
                        <span class="text-xs font-semibold text-ash">Keyword</span>
                        <input
                            type="search"
                            name="search"
                            value="{{ $active['search'] ?? '' }}"
                            placeholder="Mustang, Tesla…"
                            class="mt-1.5 w-full rounded-xl border-0 bg-ink-3 px-3.5 py-2.5 text-sm text-bone placeholder:text-ash-dim focus:outline-none focus:ring-2 focus:ring-flame"
                        >
                    </label>

                    @foreach ($sidebarFilters as $name => $filter)
                        <label class="relative mt-4 block">
                            <span class="text-xs font-semibold text-ash">{{ $filter['label'] }}</span>
                            <select
                                name="{{ $name }}"
                                class="mt-1.5 w-full cursor-pointer appearance-none rounded-xl border-0 bg-ink-3 px-3.5 py-2.5 pr-9 text-sm font-medium text-bone focus:outline-none focus:ring-2 focus:ring-flame"
                            >
                                <option value="">Any</option>
                                @foreach ($filter['options'] as $option)
                                    <option value="{{ $option }}" @selected(($active[$name] ?? null) === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                            <x-chevron class="top-[2.35rem] translate-y-0" />
                        </label>
                    @endforeach

                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <label class="block">
                            <span class="text-xs font-semibold text-ash">Min price (TSh)</span>
                            <input type="number" name="min_price" min="0" step="1000000" value="{{ $active['min_price'] ?? '' }}" placeholder="0"
                                class="mt-1.5 w-full rounded-xl border-0 bg-ink-3 px-3 py-2.5 text-sm text-bone placeholder:text-ash-dim focus:outline-none focus:ring-2 focus:ring-flame">
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold text-ash">Max price (TSh)</span>
                            <input type="number" name="max_price" min="0" step="1000000" value="{{ $active['max_price'] ?? '' }}" placeholder="Any"
                                class="mt-1.5 w-full rounded-xl border-0 bg-ink-3 px-3 py-2.5 text-sm text-bone placeholder:text-ash-dim focus:outline-none focus:ring-2 focus:ring-flame">
                        </label>
                    </div>

                    <input type="hidden" name="sort" value="{{ $sort }}">

                    <button type="submit" class="mt-6 w-full rounded-full bg-flame py-3 text-sm font-semibold text-white transition hover:bg-flame-hot">
                        Apply filters
                    </button>
                </form>
            </aside>

            {{-- ── Results ── --}}
            <div>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-2">
                        @forelse ($active as $key => $value)
                            <a
                                href="{{ route('cars.index', array_merge(request()->except([$key, 'page']), [])) }}"
                                class="group inline-flex items-center gap-1.5 rounded-full bg-ink-2 px-3 py-1.5 text-xs font-medium text-bone ring-1 ring-ink-3 transition hover:ring-flame-line"
                            >
                                {{ Str::headline($key) }}: {{ $value }}
                                <svg class="h-3 w-3 text-ash transition group-hover:text-flame" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <path d="M6 6 18 18M18 6 6 18" />
                                </svg>
                            </a>
                        @empty
                            <p class="text-sm text-ash">Showing all cars</p>
                        @endforelse
                    </div>

                    <form method="GET" action="{{ route('cars.index') }}" class="relative" data-auto-submit>
                        @foreach ($active as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <select
                            name="sort"
                            class="cursor-pointer appearance-none rounded-full bg-ink-2 py-2.5 pl-4 pr-10 text-xs font-semibold text-bone ring-1 ring-ink-3 focus:outline-none focus:ring-2 focus:ring-flame"
                        >
                            @foreach ($sortOptions as $value => $label)
                                <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-chevron class="h-3.5 w-3.5" />
                    </form>
                </div>

                @if ($cars->isEmpty())
                    <div class="mt-8 rounded-3xl bg-ink-2 p-16 text-center ring-1 ring-line">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-ink-3 text-ash">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <circle cx="11" cy="11" r="7" />
                                <path d="m20 20-3.5-3.5" />
                            </svg>
                        </span>
                        <h3 class="mt-5 text-lg font-semibold text-bone">No cars match those filters</h3>
                        <p class="mx-auto mt-2 max-w-sm text-sm text-ash">Try widening the price range or clearing a filter or two.</p>
                        <a href="{{ route('cars.index') }}" class="mt-6 inline-block rounded-full bg-flame px-6 py-3 text-sm font-semibold text-white transition hover:bg-flame-hot">
                            Clear all filters
                        </a>
                    </div>
                @else
                    <div class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($cars as $car)
                            <x-car-card :car="$car" />
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $cars->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layout>
