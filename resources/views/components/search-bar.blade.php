@props(['filters', 'active' => []])

@php
    $priceBands = [
        '0-30000' => 'Under $30,000',
        '30000-60000' => '$30,000 – $60,000',
        '60000-100000' => '$60,000 – $100,000',
        '100000-0' => '$100,000+',
    ];

    $selectedBand = collect($priceBands)->keys()->first(function (string $band) use ($active) {
        [$min, $max] = explode('-', $band);

        return ($active['min_price'] ?? null) == $min && ($active['max_price'] ?? null) == ($max === '0' ? null : $max);
    });
@endphp

<form
    method="GET"
    action="{{ route('cars.index') }}"
    {{ $attributes->class('rounded-4xl bg-white p-2.5 shadow-2xl shadow-ink-900/10 ring-1 ring-ink-900/5 sm:rounded-full') }}
>
    <div class="grid gap-1 sm:grid-cols-2 lg:grid-cols-[1.1fr_1fr_1.1fr_auto] lg:items-center">
        {{-- Location --}}
        <label class="group relative rounded-3xl px-5 py-3 transition hover:bg-ink-50/70 sm:rounded-full">
            <span class="block text-[11px] font-semibold uppercase tracking-wide text-ink-300">Location</span>
            <select name="location" class="mt-0.5 w-full cursor-pointer appearance-none bg-transparent pr-6 text-sm font-medium text-ink-900 focus:outline-none">
                <option value="">Any city</option>
                @foreach ($filters['locations'] as $city)
                    <option value="{{ $city }}" @selected(($active['location'] ?? null) === $city)>{{ $city }}</option>
                @endforeach
            </select>
            <x-chevron />
        </label>

        <span class="hidden h-9 w-px bg-ink-100 lg:block"></span>

        {{-- Body type --}}
        <label class="group relative rounded-3xl px-5 py-3 transition hover:bg-ink-50/70 sm:rounded-full">
            <span class="block text-[11px] font-semibold uppercase tracking-wide text-ink-300">Body Type</span>
            <select name="body_type" class="mt-0.5 w-full cursor-pointer appearance-none bg-transparent pr-6 text-sm font-medium text-ink-900 focus:outline-none">
                <option value="">Any type</option>
                @foreach ($filters['body_types'] as $type)
                    <option value="{{ $type }}" @selected(($active['body_type'] ?? null) === $type)>{{ $type }}</option>
                @endforeach
            </select>
            <x-chevron />
        </label>

        <span class="hidden h-9 w-px bg-ink-100 lg:block"></span>

        {{-- Price band, split into min/max on submit --}}
        <label class="group relative rounded-3xl px-5 py-3 transition hover:bg-ink-50/70 sm:rounded-full">
            <span class="block text-[11px] font-semibold uppercase tracking-wide text-ink-300">Price Range</span>
            <select data-price-band class="mt-0.5 w-full cursor-pointer appearance-none bg-transparent pr-6 text-sm font-medium text-ink-900 focus:outline-none">
                <option value="">Any price</option>
                @foreach ($priceBands as $value => $label)
                    <option value="{{ $value }}" @selected($selectedBand === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-chevron />
        </label>

        <input type="hidden" name="min_price" value="{{ $active['min_price'] ?? '' }}">
        <input type="hidden" name="max_price" value="{{ $active['max_price'] ?? '' }}">

        <button
            type="submit"
            class="mt-1 inline-flex items-center justify-center gap-2 rounded-full bg-brand-600 px-8 py-4 text-sm font-semibold text-white shadow-lg shadow-brand-600/30 transition hover:bg-brand-700 lg:mt-0"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <circle cx="11" cy="11" r="7" />
                <path d="m20 20-3.5-3.5" />
            </svg>
            Search
        </button>
    </div>
</form>
