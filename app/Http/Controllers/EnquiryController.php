<?php

namespace App\Http\Controllers;

use App\Enums\EnquiryStatus;
use App\Http\Requests\StoreEnquiryRequest;
use App\Models\Car;
use Illuminate\Http\RedirectResponse;

class EnquiryController extends Controller
{
    public function store(StoreEnquiryRequest $request, Car $car): RedirectResponse
    {
        $enquiry = $car->enquiries()->create([
            ...$request->safe()->except('website'),
            'status' => EnquiryStatus::New,
        ]);

        return redirect()
            ->route('cars.show', $car)
            ->with('status', $enquiry->type->needsPreferredDate()
                ? "Test drive requested for {$car->title}. We will call you to confirm the time."
                : "Thanks — we have reserved {$car->title} for you and will be in touch within 24 hours.");
    }
}
