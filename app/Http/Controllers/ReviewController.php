<?php

namespace App\Http\Controllers;

use App\Enums\ReviewStatus;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        return view('reviews', [
            'reviews' => Review::query()->approved()->latest()->paginate(9),
            'summary' => Review::summary(),
        ]);
    }

    public function store(StoreReviewRequest $request): RedirectResponse
    {
        Review::create([
            ...$request->safe()->except('website'),
            // Never straight to the storefront — staff read it first.
            'status' => ReviewStatus::Pending,
        ]);

        return redirect()
            ->route('reviews.index')
            ->with('status', 'Thank you — your review has been sent and will appear once our team has checked it.');
    }
}
