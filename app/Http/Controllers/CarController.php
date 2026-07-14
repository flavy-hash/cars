<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Support\Favorites;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarController extends Controller
{
    public function home(): View
    {
        return view('home', [
            'featured' => Car::where('is_featured', true)->latest('id')->take(6)->get(),
            'brands' => Car::query()
                ->selectRaw('brand, count(*) as total, min(price) as from_price')
                ->groupBy('brand')
                ->orderByDesc('total')
                ->take(6)
                ->get(),
            'stats' => [
                'listings' => Car::count(),
                'brands' => Car::distinct('brand')->count('brand'),
                'cities' => Car::distinct('city')->count('city'),
            ],
            'filters' => $this->filterOptions(),
        ]);
    }

    public function index(Request $request): View
    {
        $filters = $request->only([
            'search', 'location', 'body_type', 'brand',
            'transmission', 'fuel_type', 'condition', 'min_price', 'max_price',
        ]);

        $cars = Car::query()
            ->filter($filters)
            ->sort($request->string('sort')->toString())
            ->paginate(9)
            ->withQueryString();

        return view('listings', [
            'cars' => $cars,
            'filters' => $this->filterOptions(),
            'active' => array_filter($filters, fn ($value) => $value !== null && $value !== ''),
            'sort' => $request->string('sort')->toString(),
        ]);
    }

    public function show(Car $car): View
    {
        return view('show', [
            'car' => $car,
            'similar' => Car::where('id', '!=', $car->id)
                ->where(fn ($q) => $q->where('body_type', $car->body_type)->orWhere('brand', $car->brand))
                ->take(3)
                ->get(),
        ]);
    }

    public function favorites(Favorites $favorites): View
    {
        return view('favorites', [
            'cars' => Car::whereIn('id', $favorites->all())->get(),
        ]);
    }

    /**
     * Distinct values driving the search bar and sidebar dropdowns.
     *
     * @return array<string, \Illuminate\Support\Collection<int, string>>
     */
    private function filterOptions(): array
    {
        return [
            'locations' => Car::distinct()->orderBy('city')->pluck('city'),
            'body_types' => Car::distinct()->orderBy('body_type')->pluck('body_type'),
            'brands' => Car::distinct()->orderBy('brand')->pluck('brand'),
            'transmissions' => Car::distinct()->orderBy('transmission')->pluck('transmission'),
            'fuel_types' => Car::distinct()->orderBy('fuel_type')->pluck('fuel_type'),
            'conditions' => Car::distinct()->orderBy('condition')->pluck('condition'),
        ];
    }
}
