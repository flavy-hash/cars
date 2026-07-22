@props(['rating' => 5, 'size' => 'h-4 w-4'])

@php $rating = max(0, min(5, (int) round($rating))); @endphp

<div {{ $attributes->class('flex gap-0.5') }} role="img" aria-label="{{ $rating }} out of 5 stars">
    @for ($i = 1; $i <= 5; $i++)
        <svg class="{{ $size }} {{ $i <= $rating ? 'text-flare' : 'text-line-2' }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="m12 2 3 6.3 6.9 1-5 4.8 1.2 6.9-6.1-3.3-6.1 3.3L7.1 14l-5-4.8 6.9-1L12 2Z" />
        </svg>
    @endfor
</div>
