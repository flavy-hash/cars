<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Support\Favorites;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Car $car, Favorites $favorites): JsonResponse|RedirectResponse
    {
        $saved = $favorites->toggle($car->id);

        if ($request->wantsJson()) {
            return response()->json([
                'saved' => $saved,
                'count' => $favorites->count(),
            ]);
        }

        return back()->with('status', $saved
            ? "{$car->title} was added to your favorites."
            : "{$car->title} was removed from your favorites.");
    }
}
