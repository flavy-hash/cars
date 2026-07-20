<?php

namespace Tests\Feature;

use App\Enums\CarStatus;
use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Models\Car;
use App\Models\Enquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class EnquiryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'type' => EnquiryType::Reservation->value,
            'name' => 'Asha Mbwana',
            'email' => 'asha@example.com',
            'phone' => '+255 754 123 456',
            'message' => 'Is the service history complete?',
            ...$overrides,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('');
    }

    public function test_a_guest_can_reserve_a_car_without_an_account(): void
    {
        $car = Car::factory()->create(['status' => CarStatus::Available]);

        $this->post(route('enquiries.store', $car), $this->payload())
            ->assertRedirect(route('cars.show', $car))
            ->assertSessionHas('status');

        $enquiry = Enquiry::sole();

        $this->assertSame($car->id, $enquiry->car_id);
        $this->assertSame('Asha Mbwana', $enquiry->name);
        $this->assertSame(EnquiryType::Reservation, $enquiry->type);
        // Every new lead must land in the queue for staff to work.
        $this->assertSame(EnquiryStatus::New, $enquiry->status);
        $this->assertNull($enquiry->preferred_at);
    }

    public function test_a_test_drive_records_the_requested_date(): void
    {
        $car = Car::factory()->create();
        $when = now()->addWeek()->startOfHour();

        $this->post(route('enquiries.store', $car), $this->payload([
            'type' => EnquiryType::TestDrive->value,
            'preferred_at' => $when->format('Y-m-d H:i:s'),
        ]))->assertSessionHasNoErrors();

        $this->assertSame(
            $when->toDateTimeString(),
            Enquiry::sole()->preferred_at->toDateTimeString()
        );
    }

    public function test_a_test_drive_requires_a_future_date(): void
    {
        $car = Car::factory()->create();

        $this->post(route('enquiries.store', $car), $this->payload([
            'type' => EnquiryType::TestDrive->value,
        ]))->assertSessionHasErrors('preferred_at');

        $this->post(route('enquiries.store', $car), $this->payload([
            'type' => EnquiryType::TestDrive->value,
            'preferred_at' => now()->subDay()->toDateTimeString(),
        ]))->assertSessionHasErrors('preferred_at');

        $this->assertSame(0, Enquiry::count());
    }

    public function test_a_date_posted_with_a_reservation_is_discarded(): void
    {
        $car = Car::factory()->create();

        $this->post(route('enquiries.store', $car), $this->payload([
            'type' => EnquiryType::Reservation->value,
            'preferred_at' => now()->addWeek()->toDateTimeString(),
        ]))->assertSessionHasNoErrors();

        $this->assertNull(Enquiry::sole()->preferred_at);
    }

    public function test_contact_details_are_required_and_validated(): void
    {
        $car = Car::factory()->create();

        $this->post(route('enquiries.store', $car), [])
            ->assertSessionHasErrors(['type', 'name', 'email', 'phone']);

        $this->post(route('enquiries.store', $car), $this->payload(['email' => 'not-an-email']))
            ->assertSessionHasErrors('email');

        $this->assertSame(0, Enquiry::count());
    }

    public function test_a_sold_car_cannot_be_enquired_on(): void
    {
        $car = Car::factory()->create(['status' => CarStatus::Sold]);

        $this->post(route('enquiries.store', $car), $this->payload())->assertForbidden();

        $this->assertSame(0, Enquiry::count());
    }

    public function test_a_reserved_car_still_takes_back_up_interest(): void
    {
        $car = Car::factory()->create(['status' => CarStatus::Reserved]);

        $this->post(route('enquiries.store', $car), $this->payload())->assertSessionHasNoErrors();

        $this->assertSame(1, Enquiry::count());
    }

    public function test_the_honeypot_rejects_bots(): void
    {
        $car = Car::factory()->create();

        $this->post(route('enquiries.store', $car), $this->payload(['website' => 'http://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, Enquiry::count());
    }

    public function test_the_form_is_throttled(): void
    {
        $car = Car::factory()->create();

        for ($i = 0; $i < 6; $i++) {
            $this->post(route('enquiries.store', $car), $this->payload());
        }

        $this->post(route('enquiries.store', $car), $this->payload())->assertStatus(429);
    }

    public function test_the_detail_page_offers_the_form_only_while_the_car_is_for_sale(): void
    {
        $available = Car::factory()->create(['status' => CarStatus::Available]);
        $sold = Car::factory()->create(['status' => CarStatus::Sold]);

        $this->get(route('cars.show', $available))
            ->assertOk()
            ->assertSee('Send enquiry')
            ->assertSee('Reserve this car');

        $this->get(route('cars.show', $sold))
            ->assertOk()
            ->assertSee('This car has been sold')
            ->assertDontSee('Send enquiry');
    }

    public function test_deleting_a_car_takes_its_enquiries_with_it(): void
    {
        $enquiry = Enquiry::factory()->create();

        $enquiry->car->delete();

        $this->assertSame(0, Enquiry::count());
    }
}
