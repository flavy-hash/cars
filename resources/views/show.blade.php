@php
    $specs = [
        ['label' => 'Year', 'value' => $car->year, 'icon' => 'calendar'],
        ['label' => 'Mileage', 'value' => $car->formatted_mileage, 'icon' => 'gauge'],
        ['label' => 'Transmission', 'value' => $car->transmission, 'icon' => 'gear'],
        ['label' => 'Fuel', 'value' => $car->fuel_type, 'icon' => 'fuel'],
        ['label' => 'Horsepower', 'value' => $car->horsepower.' hp', 'icon' => 'bolt'],
        ['label' => 'Seats', 'value' => $car->seats, 'icon' => 'seat'],
        ['label' => 'Body', 'value' => $car->body_type, 'icon' => 'car'],
        ['label' => 'Colour', 'value' => $car->exterior_color, 'icon' => 'paint'],
    ];
@endphp

<x-layout :title="$car->title">
    <section class="mx-auto max-w-7xl px-4 pt-10 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs font-medium text-ash">
            <a href="{{ route('home') }}" class="transition hover:text-flame">Home</a>
            <span class="text-ash-dim">/</span>
            <a href="{{ route('cars.index') }}" class="transition hover:text-flame">Listings</a>
            <span class="text-ash-dim">/</span>
            <span class="text-bone">{{ $car->title }}</span>
        </nav>

        {{-- Gallery --}}
        <div class="mt-6 grid gap-3 lg:grid-cols-[2fr_1fr]" data-gallery>
            <div class="relative overflow-hidden rounded-4xl">
                <img
                    data-gallery-main
                    src="{{ $car->image_url }}"
                    alt="{{ $car->year }} {{ $car->title }}"
                    class="h-[24rem] w-full object-cover lg:h-[32rem]"
                >
                @if ($car->badge)
                    <span class="absolute left-5 top-5 rounded-full bg-flame px-3.5 py-1.5 text-xs font-semibold text-white shadow">
                        {{ $car->badge }}
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-4 gap-3 lg:grid-cols-2">
                @foreach ($car->gallery_urls as $index => $shot)
                    <button
                        type="button"
                        data-gallery-thumb
                        data-src="{{ $shot }}"
                        @class([
                            'overflow-hidden rounded-2xl ring-2 transition lg:h-[15.5rem]',
                            'ring-flame' => $index === 0,
                            'ring-transparent hover:ring-ink-3' => $index !== 0,
                        ])
                    >
                        <img src="{{ $shot }}" alt="" loading="lazy" class="h-20 w-full object-cover lg:h-full">
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Body --}}
        <div class="mt-10 grid gap-10 lg:grid-cols-[1fr_22rem]">
            <div>
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-bone sm:text-4xl">{{ $car->title }}</h1>
                        <p class="mt-2 flex items-center gap-1.5 text-sm text-ash">
                            <svg class="h-4 w-4 text-flame" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 21s7-5.3 7-11a7 7 0 1 0-14 0c0 5.7 7 11 7 11Z" />
                                <circle cx="12" cy="10" r="2.5" />
                            </svg>
                            {{ $car->location }}
                            <span class="ml-2 rounded-full bg-ink-3 px-2.5 py-0.5 text-[11px] font-semibold text-bone">{{ $car->condition }}</span>
                            @if ($car->status !== \App\Enums\CarStatus::Available)
                                <span @class([
                                    'rounded-full px-2.5 py-0.5 text-[11px] font-semibold',
                                    'bg-flame-soft text-flame ring-1 ring-flame-line' => $car->status === \App\Enums\CarStatus::Reserved,
                                    'bg-ink-3 text-ash' => $car->status === \App\Enums\CarStatus::Sold,
                                ])>{{ $car->status->getLabel() }}</span>
                            @endif
                        </p>
                    </div>
                    <x-favorite-button :car="$car" class="h-12 w-12 bg-ink-2 ring-1 ring-ink-3 hover:bg-flame-soft" />
                </div>

                {{-- Spec grid --}}
                <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ($specs as $spec)
                        <div class="rounded-2xl bg-ink-2 p-4 ring-1 ring-line">
                            <span class="grid h-9 w-9 place-items-center rounded-xl bg-flame-soft text-flame">
                                <x-spec-icon :name="$spec['icon']" />
                            </span>
                            <p class="mt-3 text-[11px] font-medium uppercase tracking-wide text-ash">{{ $spec['label'] }}</p>
                            <p class="mt-0.5 text-sm font-semibold text-bone">{{ $spec['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Description --}}
                <div class="mt-10">
                    <h2 class="text-lg font-bold text-bone">About this car</h2>
                    <p class="mt-3 text-sm leading-relaxed text-ash">{{ $car->description }}</p>
                </div>

                {{-- Buy / test drive --}}
                <div class="mt-10">
                    <x-enquiry-form :car="$car" />
                </div>

                {{-- Features --}}
                <div class="mt-10">
                    <h2 class="text-lg font-bold text-bone">Features &amp; equipment</h2>
                    <ul class="mt-4 grid gap-x-8 gap-y-3 sm:grid-cols-2">
                        @foreach ($car->features ?? [] as $feature)
                            <li class="flex items-center gap-2.5 text-sm text-ash">
                                <span class="grid h-5 w-5 shrink-0 place-items-center rounded-md bg-flame text-white">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m5 13 4 4L19 7" />
                                    </svg>
                                </span>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Sticky purchase panel --}}
            <aside class="lg:sticky lg:top-8 lg:self-start">
                <div class="rounded-4xl bg-ink-2 p-7 shadow-xl shadow-black/30 ring-1 ring-line">
                    <p class="text-xs font-medium text-ash">Asking price</p>
                    <p class="mt-1 text-4xl font-bold tracking-tight text-bone">{{ $car->formatted_price }}</p>
                    <p class="mt-2 text-xs text-ash">
                        or approx. <span class="font-semibold text-bone">{{ \App\Models\Car::formatMoney($car->price / 60) }}/mo</span> over 60 months
                    </p>

                    {{-- These jump to the enquiry form and preselect the matching
                         option; the data-type hook lets JS tick the radio too. --}}
                    @if ($car->acceptsEnquiries())
                        <div class="mt-6 space-y-2.5">
                            <a href="#enquire" data-enquire="reservation" class="block rounded-full bg-flame py-3.5 text-center text-sm font-semibold text-white shadow-lg shadow-flame/25 transition hover:bg-flame-hot">
                                Reserve this car
                            </a>
                            <a href="#enquire" data-enquire="test_drive" class="block rounded-full bg-flame-soft py-3.5 text-center text-sm font-semibold text-flame ring-1 ring-flame-line transition hover:bg-flame hover:text-white">
                                Book a test drive
                            </a>
                        </div>
                    @else
                        <div class="mt-6 rounded-full bg-ink-3 py-3.5 text-center text-sm font-semibold text-ash">
                            Sold
                        </div>
                    @endif

                    <div class="mt-6 space-y-3 border-t border-line pt-6">
                        @foreach (['150-point inspection passed', 'Clean title, verified history', '7-day money-back guarantee'] as $assurance)
                            <p class="flex items-center gap-2.5 text-xs text-ash">
                                <svg class="h-4 w-4 shrink-0 text-flame" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="m8.5 12 2.4 2.4 4.6-4.8" />
                                </svg>
                                {{ $assurance }}
                            </p>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-3 rounded-3xl bg-ink-3 p-5 text-bone ring-1 ring-line">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-flame-soft text-flame">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-xs text-ash">Talk to a specialist</p>
                        <p class="text-sm font-semibold">+1 (555) 019-2288</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    {{-- Similar cars --}}
    @if ($similar->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold tracking-tight text-bone">Similar cars</h2>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($similar as $item)
                    <x-car-card :car="$item" />
                @endforeach
            </div>
        </section>
    @endif
</x-layout>
