<footer class="mt-24 bg-ink-950 text-ink-200">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-[1.4fr_1fr_1fr_1fr]">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-white text-ink-950">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 17h14M5 17a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm18 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z" />
                            <path d="M3 17v-4.2a2 2 0 0 1 .27-1l2.1-3.6A2 2 0 0 1 7.1 7.2h9.8a2 2 0 0 1 1.73 1l2.1 3.6a2 2 0 0 1 .27 1V17" />
                        </svg>
                    </span>
                    <span class="leading-none">
                        <span class="block text-lg font-bold tracking-tight text-white">Velora</span>
                        <span class="block text-[11px] font-medium text-ink-300">Drive Elevated</span>
                    </span>
                </div>
                <p class="mt-5 max-w-sm text-sm leading-relaxed text-ink-300">
                    Velora is where people buy and sell cars without the guesswork. Every listing is inspected,
                    history-checked and priced against real market data.
                </p>
                <div class="mt-6 flex gap-3">
                    @foreach (['M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3Z', 'M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3Z', 'M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6ZM6 9H2v12h4V9Zm-2-3a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z'] as $path)
                        <a href="#" class="grid h-9 w-9 place-items-center rounded-full bg-white/5 text-ink-300 ring-1 ring-white/10 transition hover:bg-white/10 hover:text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $path }}" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>

            @php
                $columns = [
                    'Explore' => [
                        ['Browse listings', route('cars.index')],
                        ['New arrivals', route('cars.index', ['condition' => 'New'])],
                        ['Certified cars', route('cars.index', ['condition' => 'Certified'])],
                        ['Saved cars', route('favorites.index')],
                    ],
                    'Body types' => [
                        ['SUVs', route('cars.index', ['body_type' => 'SUV'])],
                        ['Sedans', route('cars.index', ['body_type' => 'Sedan'])],
                        ['Coupes', route('cars.index', ['body_type' => 'Coupe'])],
                        ['Convertibles', route('cars.index', ['body_type' => 'Convertible'])],
                    ],
                    'Company' => [
                        ['About us', route('home').'#about'],
                        ['How it works', route('home').'#about'],
                        ['Careers', '#'],
                        ['Contact', '#'],
                    ],
                ];
            @endphp

            @foreach ($columns as $heading => $items)
                <div>
                    <h3 class="text-sm font-semibold text-white">{{ $heading }}</h3>
                    <ul class="mt-4 space-y-3">
                        @foreach ($items as [$label, $href])
                            <li>
                                <a href="{{ $href }}" class="text-sm text-ink-300 transition hover:text-white">{{ $label }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="mt-14 flex flex-col gap-4 border-t border-white/10 pt-8 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-ink-300">© {{ now()->year }} Velora Motors. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="#" class="text-sm text-ink-300 transition hover:text-white">Privacy Policy</a>
                <a href="#" class="text-sm text-ink-300 transition hover:text-white">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
