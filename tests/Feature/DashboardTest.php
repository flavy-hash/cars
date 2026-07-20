<?php

namespace Tests\Feature;

use App\Enums\CarStatus;
use App\Enums\EnquiryStatus;
use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Filament\Widgets\BusinessOverview;
use App\Filament\Widgets\EnquiriesChart;
use App\Filament\Widgets\LatestEnquiries;
use App\Models\Car;
use App\Models\Enquiry;
use App\Models\User;
use App\Support\DailyCounts;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_the_dashboard_renders_for_an_admin(): void
    {
        $this->actingAs($this->admin)->get('/admin')->assertOk();
    }

    public function test_the_overview_counts_what_is_actually_in_the_database(): void
    {
        $forSale = Car::factory()->count(3)->create(['status' => CarStatus::Available, 'price' => 10_000_000]);
        Car::factory()->count(2)->create(['status' => CarStatus::Reserved, 'price' => 50_000_000]);
        Car::factory()->create(['status' => CarStatus::Sold, 'price' => 99_000_000]);

        // Hang the leads off an existing car — EnquiryFactory would otherwise
        // spin up a car of its own for each one and skew the totals.
        Enquiry::factory()->count(4)->unworked()->create(['car_id' => $forSale->first()->id]);
        Enquiry::factory()->count(2)->create(['status' => EnquiryStatus::Won, 'car_id' => $forSale->first()->id]);

        Livewire::actingAs($this->admin)
            ->test(BusinessOverview::class)
            ->assertOk()
            ->assertSee('Leads to answer')
            ->assertSee('4')          // only the unworked ones
            ->assertSee('6 listed in total')
            ->assertSee('1 sold all time')
            // Inventory value counts cars still for sale, not reserved or sold.
            ->assertSee('TSh 30,000,000');
    }

    public function test_the_overview_says_so_when_there_is_nothing_to_chase(): void
    {
        Enquiry::factory()->create(['status' => EnquiryStatus::Won]);

        Livewire::actingAs($this->admin)
            ->test(BusinessOverview::class)
            ->assertSee('All caught up');
    }

    public function test_the_chart_renders(): void
    {
        Enquiry::factory()->count(2)->create();

        Livewire::actingAs($this->admin)
            ->test(EnquiriesChart::class)
            ->assertOk()
            ->assertSee('Enquiries per day');
    }

    public function test_quiet_days_are_zero_filled_rather_than_skipped(): void
    {
        // Two leads today and one a week ago; every day between must still appear,
        // or the line would silently close the gap and misreport the trend.
        Enquiry::factory()->count(2)->create(['created_at' => now()]);
        Enquiry::factory()->create(['created_at' => now()->subDays(6)]);

        $counts = DailyCounts::forEnquiries(days: 7);

        $this->assertCount(7, $counts);
        $this->assertSame(2, $counts[now()->toDateString()]);
        $this->assertSame(1, $counts[now()->subDays(6)->toDateString()]);
        $this->assertSame(0, $counts[now()->subDays(3)->toDateString()]);
        // Oldest first, so the line reads left to right.
        $this->assertSame(now()->subDays(6)->toDateString(), array_key_first($counts));
    }

    public function test_the_chart_ignores_leads_older_than_the_window(): void
    {
        Enquiry::factory()->create(['created_at' => now()->subDays(30)]);
        Enquiry::factory()->create(['created_at' => now()]);

        $counts = DailyCounts::forEnquiries(days: 14);

        $this->assertSame(1, array_sum($counts));
    }

    public function test_the_latest_enquiries_widget_shows_newest_first(): void
    {
        $old = Enquiry::factory()->create(['name' => 'Older Buyer', 'created_at' => now()->subDay()]);
        $new = Enquiry::factory()->create(['name' => 'Newer Buyer', 'created_at' => now()]);

        Livewire::actingAs($this->admin)
            ->test(LatestEnquiries::class)
            ->assertOk()
            ->assertSee('Newer Buyer')
            ->assertCanSeeTableRecords([$new, $old], inOrder: true);
    }

    public function test_the_latest_enquiries_widget_caps_the_list_at_five(): void
    {
        // Eight leads, oldest first, so the last five created are the newest.
        $leads = collect(range(7, 0))->map(fn (int $daysAgo) => Enquiry::factory()->create([
            'name' => "Buyer {$daysAgo}",
            'created_at' => now()->subDays($daysAgo),
        ]));

        Livewire::actingAs($this->admin)
            ->test(LatestEnquiries::class)
            ->assertCanSeeTableRecords($leads->take(-5))
            ->assertCanNotSeeTableRecords($leads->take(3));
    }

    public function test_the_dashboard_view_button_links_to_the_full_record(): void
    {
        // It must be a link, not a modal: this table belongs to a widget rather
        // than the resource, so a modal would have no infolist and open empty.
        $enquiry = Enquiry::factory()->create();
        $expected = EnquiryResource::getUrl('view', ['record' => $enquiry]);

        $action = Livewire::actingAs($this->admin)
            ->test(LatestEnquiries::class)
            ->instance()
            ->getTable()
            ->getAction('view');

        $this->assertSame($expected, $action->record($enquiry)->getUrl());
    }

    public function test_the_dashboard_row_click_opens_the_lead(): void
    {
        $enquiry = Enquiry::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(LatestEnquiries::class)
            ->assertSee(EnquiryResource::getUrl('view', ['record' => $enquiry]), escape: false);
    }

    public function test_a_non_admin_cannot_see_the_dashboard(): void
    {
        $visitor = User::factory()->create(['is_admin' => false]);

        $this->actingAs($visitor)->get('/admin')->assertForbidden();
    }
}
