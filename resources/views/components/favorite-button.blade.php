@props(['car'])

@php $saved = in_array($car->id, $favoriteIds, true); @endphp

<form method="POST" action="{{ route('favorites.toggle', $car) }}" data-favorite-form>
    @csrf
    <button
        type="submit"
        aria-pressed="{{ $saved ? 'true' : 'false' }}"
        aria-label="{{ $saved ? 'Remove from favorites' : 'Save to favorites' }}"
        {{ $attributes->class([
            'group/fav grid place-items-center rounded-full transition',
            'aria-pressed:text-rose-500 text-ink-300 hover:text-rose-500',
        ]) }}
    >
        <svg class="h-5 w-5 transition group-aria-pressed/fav:fill-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21.2l7.7-7.7 1.1-1.1a5.5 5.5 0 0 0 0-7.8Z" />
        </svg>
    </button>
</form>
