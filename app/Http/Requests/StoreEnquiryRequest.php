<?php

namespace App\Http\Requests;

use App\Enums\EnquiryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Anyone may enquire — but not on a car that has already sold.
        return $this->route('car')->acceptsEnquiries();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(EnquiryType::class)],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'min:7', 'max:30'],
            'message' => ['nullable', 'string', 'max:2000'],

            // Only a test drive asks for a date, and it has to be in the future.
            'preferred_at' => [
                Rule::requiredIf(fn () => $this->input('type') === EnquiryType::TestDrive->value),
                'nullable',
                'date',
                'after:now',
                'before:'.now()->addYear()->toDateString(),
            ],

            // Honeypot: a real person never fills this in, bots fill everything.
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'preferred_at.required' => 'Please tell us when you would like to drive it.',
            'preferred_at.after' => 'Please pick a date in the future.',
            'website.prohibited' => 'That submission looked automated. Please try again.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // A reservation has no date; drop anything posted so it cannot be smuggled in.
        if ($this->input('type') !== EnquiryType::TestDrive->value) {
            $this->merge(['preferred_at' => null]);
        }
    }
}
