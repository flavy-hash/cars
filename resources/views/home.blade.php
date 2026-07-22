@php
    $phone = config('contact.phone');
    $whatsapp = 'https://wa.me/'.preg_replace('/\D/', '', $phone);

    $steps = [
        ['n' => 1, 'h' => 'Pick and deposit', 'p' => 'We send you real auction sheets with grades and inspection notes. You approve a maximum bid and deposit 20%.', 'when' => 'Day 1–3'],
        ['n' => 2, 'h' => 'We win the bid', 'p' => 'If we lose the lot, we bid the next one at no extra cost. You only pay the car price once a bid is won.', 'when' => 'Day 3–10'],
        ['n' => 3, 'h' => 'Shipping to Dar', 'p' => 'RoRo from Yokohama or Nagoya to Dar es Salaam. You get the bill of lading and the vessel tracking link.', 'when' => 'Day 10–28'],
        ['n' => 4, 'h' => 'Clearing and plates', 'p' => 'We pay TRA duty, clear the port, drive it up to {area} and hand over the registration card and plates.', 'when' => 'Day 28–35'],
    ];
@endphp

<x-layout>
    {{-- ───────────────────────── Hero ───────────────────────── --}}
    <div class="relative overflow-hidden border-b border-line bg-paper">
        <div class="absolute inset-0" aria-hidden="true">
            <img src="{{ $homepage->hero_image_url }}" alt="" fetchpriority="high" class="h-full w-full object-cover object-[70%_50%]">
            {{-- Washes the photo out behind the words so the headline stays readable. --}}
            <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(255,255,255,.97)_55%,rgba(255,255,255,.62)_100%)] lg:bg-[linear-gradient(96deg,#fff_26%,rgba(255,255,255,.94)_48%,rgba(255,255,255,.42)_78%,rgba(255,255,255,.15)_100%)]"></div>
            {{-- Second, vertical fade: the stat band runs the full width, so its
                 right-hand end would otherwise sit on bare photo and be unreadable
                 whenever the admin uploads a dark hero image. --}}
            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-paper via-paper/85 to-transparent"></div>
        </div>
        <div class="stripe-wash pointer-events-none absolute -inset-x-[15%] -inset-y-1/4 opacity-50" aria-hidden="true"></div>

        <div class="relative mx-auto w-full max-w-[1280px] px-[clamp(16px,4.5vw,60px)] pt-[clamp(38px,6.5vw,80px)]">
            <p class="data inline-flex flex-wrap items-center gap-2 rounded-sm border border-flare/35 bg-flare/[.06] px-3 py-1.5 text-[11px] text-flare-dk">
                {{ $homepage->about_eyebrow ?: config('contact.area') }}
                <b class="font-semibold text-ink">· {{ $homepage->trust_badge ?: $stats['listings'].' cars in the yard today' }}</b>
            </p>

            <h1 class="mt-5 max-w-[15ch] text-[clamp(32px,6.6vw,64px)] font-bold">
                {!! nl2br(e($homepage->hero_heading)) !!}
            </h1>

            <p class="mt-[18px] max-w-[54ch] text-[clamp(15px,1.3vw,17.5px)] font-light text-body">
                {{ $homepage->hero_subheading }}
            </p>

            <div class="mt-8">
                <x-search-bar :filters="$filters" />
            </div>

            {{-- Live counts where we have them, so the band never goes stale. --}}
            <div class="mt-9 grid grid-cols-[repeat(auto-fit,minmax(140px,1fr))] border-t border-line">
                @foreach ([
                    [$stats['listings'], 'cars in the yard'],
                    filled($homepage->stat_value)
                        ? [$homepage->stat_value, $homepage->stat_label]
                        : ['28–35', 'days, Japan auction to your gate'],
                    [$stats['brands'], 'makes on the lot'],
                    [$stats['cities'], 'towns we deliver to'],
                ] as [$value, $caption])
                    <div class="border-r border-line py-4 pb-6 pr-3.5 last:border-r-0 max-sm:border-b max-sm:border-r-0 max-sm:last:border-b-0">
                        <strong class="block text-[clamp(20px,2.4vw,26px)] font-bold tracking-[-.02em] text-ink">{{ $value }}</strong>
                        <span class="text-[12.5px] text-muted">{{ $caption }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ───────────────────────── Stock ───────────────────────── --}}
    <section id="stock" class="mx-auto w-full max-w-[1280px] px-[clamp(16px,4.5vw,60px)] py-[clamp(48px,7.5vw,92px)]">
        <div class="mb-7">
            <span class="data mb-2.5 block text-[10.5px] text-flare">In the yard now</span>
            <h2 class="max-w-[18ch] text-[clamp(23px,3.9vw,38px)]">Cars you can drive home this week</h2>
            <p class="mt-2 max-w-[46ch] text-[15px] font-light text-muted">
                Every one is physically in {{ config('contact.area') }}, inspected on a lift, and sold with its registration card and service history in your hand.
            </p>
        </div>

        @if ($featured->isNotEmpty())
            <div class="grid grid-cols-[repeat(auto-fill,minmax(min(100%,300px),1fr))] gap-[22px]">
                @foreach ($featured as $car)
                    <x-car-card :car="$car" />
                @endforeach
            </div>

            <p class="mt-[26px] text-[14.5px] text-muted">
                Showing {{ $featured->count() }} of {{ $stats['listings'] }}.
                <a href="{{ route('cars.index') }}" class="font-medium text-flare no-underline hover:underline">See the full stock list</a>
                — updated every morning.
            </p>
        @else
            <div class="border border-dashed border-line-2 bg-mist p-[clamp(28px,6vw,44px)] text-center text-muted">
                <b class="mb-1.5 block text-lg font-semibold text-ink">No cars listed yet</b>
                Add stock from the admin and it appears here straight away.
            </div>
        @endif
    </section>

    {{-- ───────────────────────── Import to order ───────────────────────── --}}
    <section class="bg-ink py-[clamp(48px,7.5vw,92px)] text-[#d8dbe0]">
        <div class="mx-auto w-full max-w-[1280px] px-[clamp(16px,4.5vw,60px)]">
            <div class="mb-7">
                <span class="data mb-2.5 block text-[10.5px] text-flare">Import to order</span>
                <h2 class="max-w-[18ch] text-[clamp(23px,3.9vw,38px)] text-white">Don't see it? We buy it at the Japan auction for you.</h2>
                <p class="mt-2 max-w-[46ch] text-[15px] font-light text-[#9aa1ac]">
                    You pick the grade and the budget, we bid, and you pay the landed cost — auction price, freight, TRA duty and clearing, all itemised before we bid.
                </p>
            </div>

            <div class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,210px),1fr))] border border-[#2b2f36] bg-ink-2">
                @foreach ($steps as $step)
                    <div class="border-r border-[#2b2f36] px-[22px] py-6 last:border-r-0 max-sm:border-b max-sm:border-r-0 max-sm:last:border-b-0">
                        <div class="mb-3.5 grid h-[26px] w-[30px] -skew-x-[18deg] place-items-center bg-flare text-[13px] font-bold text-white">
                            <span class="skew-x-[18deg]">{{ $step['n'] }}</span>
                        </div>
                        <h3 class="mb-2 text-[17px] text-white">{{ $step['h'] }}</h3>
                        <p class="text-[14.5px] font-light text-[#9aa1ac]">{{ $step['p'] }}</p>
                        <span class="data mt-3 block text-[10.5px] text-flare">{{ $step['when'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="mt-[22px] flex flex-wrap items-center justify-between gap-4 bg-white p-[clamp(18px,3vw,26px)] text-body">
                <p class="max-w-[58ch] text-[15px] font-light">
                    <b class="font-semibold text-ink">Duty is the part people get wrong.</b>
                    On a 2015 2.8L diesel the excise band, import duty, VAT and the railway levy together often add more than half the auction price. Send us the chassis number and we compute it on TRA's current rates before you commit a shilling.
                </p>
                <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="flex min-h-[44px] items-center justify-center bg-flare px-6 text-sm font-semibold text-white no-underline transition hover:bg-flare-hot max-sm:w-full">
                    Get a duty estimate
                </a>
            </div>
        </div>
    </section>

    {{-- ───────────────────────── Financing ───────────────────────── --}}
    <section id="finance" class="bg-mist py-[clamp(48px,7.5vw,92px)]">
        <div class="mx-auto w-full max-w-[1280px] px-[clamp(16px,4.5vw,60px)]">
            <div class="mb-7">
                <span class="data mb-2.5 block text-[10.5px] text-flare">Paying for it</span>
                <h2 class="max-w-[18ch] text-[clamp(23px,3.9vw,38px)]">Loan, trade-in, or a bit of both</h2>
                <p class="mt-2 max-w-[46ch] text-[15px] font-light text-muted">
                    We prepare the bank file — valuation, pro-forma, insurance quote — so your loan doesn't sit for three weeks waiting for one missing paper.
                </p>
            </div>

            <div class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,320px),1fr))] gap-[22px]">
                {{-- Loan calculator --}}
                <div class="min-w-0 border border-line bg-white p-[clamp(20px,3vw,28px)] shadow-[0_1px_2px_rgba(20,22,26,.05),0_8px_24px_rgba(20,22,26,.06)]">
                    <h3 class="mb-2.5 text-[clamp(18px,2.3vw,21px)]">Monthly payment estimate</h3>
                    <p class="mt-0 text-[14.5px] font-light text-muted">Indicative only, at 18% p.a. reducing balance. Your bank sets the final rate and fees.</p>

                    <div class="mt-1.5 grid gap-4" data-loan-calculator>
                        <div>
                            <label for="c-price" class="mb-1.5 block text-xs text-muted">
                                Car price — <span class="text-base font-semibold text-ink" data-out="price">TSh 42,000,000</span>
                            </label>
                            <input id="c-price" data-input="price" type="range" min="8000000" max="200000000" step="500000" value="42000000" class="min-h-7 w-full accent-flare">
                        </div>
                        <div>
                            <label for="c-down" class="mb-1.5 block text-xs text-muted">
                                Down payment — <span class="text-base font-semibold text-ink" data-out="down">30%</span>
                            </label>
                            <input id="c-down" data-input="down" type="range" min="20" max="70" step="5" value="30" class="min-h-7 w-full accent-flare">
                        </div>
                        <div>
                            <label for="c-term" class="mb-1.5 block text-xs text-muted">
                                Term — <span class="text-base font-semibold text-ink" data-out="term">36 months</span>
                            </label>
                            <input id="c-term" data-input="term" type="range" min="12" max="60" step="6" value="36" class="min-h-7 w-full accent-flare">
                        </div>

                        <div class="mt-1.5 flex flex-wrap items-end justify-between gap-3.5 border-t border-line pt-[18px]">
                            <div>
                                <span class="block text-[clamp(24px,3.4vw,31px)] font-bold tracking-[-.02em] text-flare" data-out="monthly">TSh 0</span>
                                <small class="block max-w-[34ch] text-[12.5px] text-muted">
                                    per month · you pay <span data-out="deposit">—</span> upfront
                                </small>
                            </div>
                            <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="inline-flex min-h-[44px] items-center justify-center border border-line-2 bg-white px-4 text-sm font-medium text-ink no-underline transition hover:border-ink">
                                Start a loan file
                            </a>
                        </div>
                    </div>

                    <div class="mt-[18px] flex flex-wrap gap-2">
                        @foreach (['CRDB', 'NMB', 'NBC', 'Exim', 'Stanbic', 'Absa'] as $bank)
                            <span class="border border-line-2 px-3 py-1.5 text-[12.5px] font-medium text-body">{{ $bank }}</span>
                        @endforeach
                    </div>
                </div>

                {{-- Trade-in. Heading, copy and bullets come from the Home page editor. --}}
                <div class="min-w-0 border border-line bg-white p-[clamp(20px,3vw,28px)] shadow-[0_1px_2px_rgba(20,22,26,.05),0_8px_24px_rgba(20,22,26,.06)]">
                    <h3 class="mb-2.5 text-[clamp(18px,2.3vw,21px)]">{{ str_replace("\n", ' ', $homepage->about_heading) }}</h3>
                    <p class="mt-0 text-[14.5px] font-light text-muted">{{ $homepage->about_body }}</p>

                    <ul class="mt-4 grid list-none gap-3.5 p-0">
                        @foreach ($homepage->points as $point)
                            <li class="grid grid-cols-[18px_1fr] gap-2.5 text-[14.5px] font-light text-body">
                                <i class="not-italic font-bold text-flare">→</i>
                                <span>{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="mt-[22px] inline-flex min-h-[44px] items-center justify-center bg-flare px-6 text-sm font-semibold text-white no-underline transition hover:bg-flare-hot">
                        Book a valuation
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ───────────────────────── Reviews ───────────────────────── --}}
    @if ($reviews->isNotEmpty())
        <section class="mx-auto w-full max-w-[1280px] px-[clamp(16px,4.5vw,60px)] py-[clamp(48px,7.5vw,92px)]">
            <div class="mb-7 flex flex-wrap items-end justify-between gap-x-8 gap-y-4">
                <div>
                    <span class="data mb-2.5 block text-[10.5px] text-flare">What buyers say</span>
                    <h2 class="max-w-[18ch] text-[clamp(23px,3.9vw,38px)]">People who stopped dreading car buying</h2>
                    @if ($reviewSummary['total'] > 0)
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            <x-stars :rating="$reviewSummary['average']" />
                            <p class="text-sm text-muted">
                                <span class="font-semibold text-ink">{{ number_format($reviewSummary['average'], 1) }}</span>
                                from {{ $reviewSummary['total'] }} {{ Str::plural('review', $reviewSummary['total']) }}
                            </p>
                        </div>
                    @endif
                </div>
                <a href="{{ route('reviews.index') }}" class="text-sm font-medium text-flare no-underline hover:underline">Read all reviews →</a>
            </div>

            <div class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,300px),1fr))] gap-[22px]">
                @foreach ($reviews as $review)
                    <x-review-card :review="$review" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ───────────────────────── Visit ───────────────────────── --}}
    <section id="visit" class="mx-auto w-full max-w-[1280px] px-[clamp(16px,4.5vw,60px)] pb-[clamp(48px,7.5vw,92px)]">
        <div class="mb-7">
            <span class="data mb-2.5 block text-[10.5px] text-flare">Come and see</span>
            <h2 class="max-w-[18ch] text-[clamp(23px,3.9vw,38px)]">Come and see us in {{ config('contact.area') }}</h2>
            <p class="mt-2 max-w-[46ch] text-[15px] font-light text-muted">
                {{ config('contact.directions') }}
            </p>
        </div>

        <div class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,300px),1fr))] border border-line bg-white">
            <div class="min-w-0 p-[clamp(20px,3vw,32px)]">
                <div class="grid gap-[18px]">
                    @foreach ([
                        ['Address', config('contact.address'), config('contact.directions')],
                        ['Open', config('contact.hours'), 'Sunday by appointment only'],
                        ['Talk to us', $phone, 'WhatsApp is fastest — send the car name and we reply with a video walkaround'],
                        ['We accept', 'Cash, bank transfer, bank loan', 'Mobile money for deposits up to TSh 5M'],
                    ] as [$k, $v, $note])
                        <div>
                            <span class="data block text-[10.5px] text-flare">{{ $k }}</span>
                            <span class="mt-1.5 block break-words text-base font-medium text-ink">
                                {{ $v }}
                                <span class="block text-[13.5px] font-light text-muted">{{ $note }}</span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid min-h-[300px] bg-mist p-0">
                <svg viewBox="0 0 400 300" preserveAspectRatio="xMidYMid slice" aria-label="Sketch map of the yard" class="h-full w-full">
                    <rect width="400" height="300" fill="#f5f6f8" />
                    <g stroke="#e4e6eb" stroke-width="1"><path d="M0 60H400M0 130H400M0 210H400M70 0V300M180 0V300M300 0V300" /></g>
                    <path d="M-20 240 L420 90" stroke="#dfe2e8" stroke-width="22" fill="none" />
                    <path d="M-20 240 L420 90" stroke="#b9bfc9" stroke-width="1.5" stroke-dasharray="12 12" fill="none" />
                    <text x="292" y="126" fill="#7b8492" font-family="Poppins, sans-serif" font-size="11" letter-spacing="2">MAIN ROAD</text>
                    <g transform="translate(168,150)">
                        <path d="M0 0 L34 0 L26 26 L-8 26 Z" fill="#ff4713" />
                        <circle cx="13" cy="13" r="30" fill="none" stroke="#ff4713" stroke-opacity=".4" stroke-width="1.5" />
                    </g>
                    <text x="150" y="212" fill="#14161a" font-family="Poppins, sans-serif" font-size="14" font-weight="600">MNENE MOTORS</text>
                    <text x="150" y="230" fill="#7b8492" font-family="Poppins, sans-serif" font-size="11.5">Ask for MNENE Motors</text>
                </svg>
            </div>
        </div>
    </section>
</x-layout>
