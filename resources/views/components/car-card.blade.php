@props(['car'])

@php
    $badgeStyles = [
        'Featured' => 'bg-brand-100 text-brand-700',
        'New' => 'bg-white text-ink-900',
        'Hot Deal' => 'bg-gradient-to-r from-fuchsia-500 to-rose-500 text-white',
    ];
@endphp

<article {{ $attributes->class('group flex flex-col overflow-hidden rounded-3xl bg-white p-3 shadow-sm shadow-ink-900/5 ring-1 ring-ink-900/5 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-ink-900/10') }}>
    <div class="relative overflow-hidden rounded-2xl">
        <a href="{{ route('cars.show', $car) }}">
            <img
                src="{{ $car->image }}"
                alt="{{ $car->year }} {{ $car->title }}"
                loading="lazy"
                class="h-52 w-full object-cover transition duration-500 group-hover:scale-105"
            >
        </a>

        @if ($car->badge)
            <span class="absolute left-3 top-3 rounded-full px-3 py-1 text-[11px] font-semibold shadow-sm {{ $badgeStyles[$car->badge] ?? 'bg-white text-ink-900' }}">
                {{ $car->badge }}
            </span>
        @endif

        <span class="absolute bottom-3 right-3 rounded-full bg-ink-950/70 px-2.5 py-1 text-[11px] font-medium text-white backdrop-blur">
            {{ $car->formatted_mileage }}
        </span>
    </div>

    <div class="flex flex-1 flex-col px-2 pb-1 pt-4">
        <h3 class="text-base font-semibold tracking-tight text-ink-900">
            <a href="{{ route('cars.show', $car) }}" class="transition hover:text-brand-600">{{ $car->title }}</a>
        </h3>

        <p class="mt-1.5 flex items-center gap-1.5 text-sm text-ink-500">
            <svg class="h-4 w-4 text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 21s7-5.3 7-11a7 7 0 1 0-14 0c0 5.7 7 11 7 11Z" />
                <circle cx="12" cy="10" r="2.5" />
            </svg>
            {{ $car->location }}
        </p>

        <div class="mt-4 flex flex-wrap gap-1.5 text-[11px] font-medium text-ink-500">
            <span class="rounded-full bg-ink-50 px-2.5 py-1">{{ $car->year }}</span>
            <span class="rounded-full bg-ink-50 px-2.5 py-1">{{ $car->transmission }}</span>
            <span class="rounded-full bg-ink-50 px-2.5 py-1">{{ $car->fuel_type }}</span>
        </div>

        <div class="mt-auto flex items-center justify-between pt-5">
            <p class="text-lg font-bold text-ink-900">
                {{ $car->formatted_price }}
                <span class="text-xs font-medium text-ink-300">{{ $car->condition }}</span>
            </p>
            <x-favorite-button :car="$car" class="h-10 w-10 bg-ink-50 hover:bg-rose-50" />
        </div>
    </div>
</article>
