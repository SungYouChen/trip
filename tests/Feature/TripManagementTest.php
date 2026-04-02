<?php

namespace Tests\Feature;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a user can see their own trips in index.
     */
    public function test_user_can_view_own_trips()
    {
        $user = User::factory()->create(['username' => 'elk']);
        $trip = Trip::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('home', ['user' => $user->username]));

        $response->assertStatus(200);
        $response->assertSee($trip->name);
    }

    /**
     * Test a user cannot view another user's private index (user.scope middleware).
     */
    public function test_user_cannot_access_others_scope()
    {
        $userA = User::factory()->create(['username' => 'UserA']);
        $userB = User::factory()->create(['username' => 'UserB']);

        $response = $this->actingAs($userA)->get(route('home', ['user' => $userB->username]));
        
        // The middleware redirects back to own home with error
        $response->assertStatus(302);
        $response->assertRedirect(route('home', ['user' => $userA->username]));
    }

    /**
     * Test AJAX Trip Creation.
     */
    public function test_ajax_trip_creation()
    {
        $user = User::factory()->create(['username' => 'elk']);
        
        $response = $this->actingAs($user)->postJson(route('trips.store', ['user' => $user->username]), [
            'name' => '東京櫻花季',
            'start_date' => '2025-04-01',
            'end_date' => '2025-04-07',
            'base_currency' => 'TWD',
            'target_currency' => 'JPY',
            'exchange_rate' => 0.21
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('trips', ['name' => '東京櫻花季', 'user_id' => $user->id]);
    }

    /**
     * Test Trip Archiving (Soft Delete).
     */
    public function test_trip_archiving()
    {
        $user = User::factory()->create(['username' => 'elk']);
        $trip = Trip::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson(route('trips.destroy', ['user' => $user->username, 'trip' => $trip->id]), [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertSoftDeleted('trips', ['id' => $trip->id]);
    }

    /**
     * Test Trip Restoration.
     */
    public function test_trip_restoration()
    {
        $user = User::factory()->create(['username' => 'elk']);
        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $trip->delete(); // Soft delete first

        $response = $this->actingAs($user)->patchJson(route('trips.restore', ['user' => $user->username, 'tripId' => $trip->id]), [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'deleted_at' => null]);
    }

    /**
     * Test Profile Background Settings Update.
     */
    public function test_ajax_profile_update()
    {
        $user = User::factory()->create(['username' => 'elk']);

        $response = $this->actingAs($user)->postJson(route('profile.update', ['user' => $user->username]), [
            'name' => 'New Name',
            'bg_opacity' => 50,
            'bg_blur' => 10,
            'bg_width' => 60
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals(50, $user->bg_opacity);
        $this->assertEquals(10, $user->bg_blur);
        $this->assertEquals(60, $user->bg_width);
    }
}
