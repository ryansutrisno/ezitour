<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: payment-integration
 * Task 16: Update Dashboard to Show Payment Status
 * 
 * Tests untuk memverifikasi:
 * - Dashboard menampilkan payment status
 * - Filter paid/unpaid bookings berfungsi
 * - Payment status badges ditampilkan
 */
class DashboardFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Package $package;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        
        $this->package = Package::create([
            'name' => 'Test Package',
            'slug' => 'test-package',
            'description' => 'Test package description',
            'total_price' => 1000000,
        ]);
    }

    /**
     * Test dashboard displays all bookings by default
     */
    public function test_dashboard_displays_all_bookings(): void
    {
        // Create paid and unpaid bookings
        $paidBooking = Booking::create([
            'user_id' => $this->user->id,
            'package_id' => $this->package->id,
            'travel_date' => now()->addDays(7),
            'total_amount' => 1000000,
            'status' => 'paid',
            'pickup_location' => 'Test Location',
            'payment_date' => now(),
        ]);

        $unpaidBooking = Booking::create([
            'user_id' => $this->user->id,
            'package_id' => $this->package->id,
            'travel_date' => now()->addDays(14),
            'total_amount' => 1500000,
            'status' => 'pending',
            'pickup_location' => 'Test Location 2',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.index'));

        $response->assertStatus(200);
        $response->assertViewHas('bookings');
        $response->assertViewHas('allCount', 2);
        $response->assertViewHas('paidCount', 1);
        $response->assertViewHas('unpaidCount', 1);
    }

    /**
     * Test dashboard filter shows only paid bookings
     */
    public function test_dashboard_filter_paid_bookings(): void
    {
        Booking::create([
            'user_id' => $this->user->id,
            'package_id' => $this->package->id,
            'travel_date' => now()->addDays(7),
            'total_amount' => 1000000,
            'status' => 'paid',
            'pickup_location' => 'Test Location',
            'payment_date' => now(),
        ]);

        Booking::create([
            'user_id' => $this->user->id,
            'package_id' => $this->package->id,
            'travel_date' => now()->addDays(14),
            'total_amount' => 1500000,
            'status' => 'pending',
            'pickup_location' => 'Test Location 2',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.index', ['payment_status' => 'paid']));

        $response->assertStatus(200);
        $response->assertViewHas('paymentFilter', 'paid');
        
        $bookings = $response->viewData('bookings');
        $this->assertCount(1, $bookings);
        $this->assertEquals('paid', $bookings->first()->status);
    }

    /**
     * Test dashboard filter shows only unpaid bookings
     */
    public function test_dashboard_filter_unpaid_bookings(): void
    {
        Booking::create([
            'user_id' => $this->user->id,
            'package_id' => $this->package->id,
            'travel_date' => now()->addDays(7),
            'total_amount' => 1000000,
            'status' => 'paid',
            'pickup_location' => 'Test Location',
            'payment_date' => now(),
        ]);

        Booking::create([
            'user_id' => $this->user->id,
            'package_id' => $this->package->id,
            'travel_date' => now()->addDays(14),
            'total_amount' => 1500000,
            'status' => 'pending',
            'pickup_location' => 'Test Location 2',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.index', ['payment_status' => 'unpaid']));

        $response->assertStatus(200);
        $response->assertViewHas('paymentFilter', 'unpaid');
        
        $bookings = $response->viewData('bookings');
        $this->assertCount(1, $bookings);
        $this->assertEquals('pending', $bookings->first()->status);
    }

    /**
     * Test dashboard shows correct counts in filter badges
     */
    public function test_dashboard_shows_correct_filter_counts(): void
    {
        // Create 2 paid and 3 unpaid bookings
        for ($i = 0; $i < 2; $i++) {
            Booking::create([
                'user_id' => $this->user->id,
                'package_id' => $this->package->id,
                'travel_date' => now()->addDays($i + 1),
                'total_amount' => 1000000,
                'status' => 'paid',
                'pickup_location' => 'Test Location',
                'payment_date' => now(),
            ]);
        }

        for ($i = 0; $i < 3; $i++) {
            Booking::create([
                'user_id' => $this->user->id,
                'package_id' => $this->package->id,
                'travel_date' => now()->addDays($i + 10),
                'total_amount' => 1500000,
                'status' => 'pending',
                'pickup_location' => 'Test Location',
            ]);
        }

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.index'));

        $response->assertStatus(200);
        $response->assertViewHas('allCount', 5);
        $response->assertViewHas('paidCount', 2);
        $response->assertViewHas('unpaidCount', 3);
    }
}
