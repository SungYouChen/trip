<?php

namespace Tests\Feature;

use App\Models\ItineraryDay;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SharingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a guest can view a shared trip index via token.
     */
    public function test_guest_view_shared_trip_index()
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create(['user_id' => $user->id, 'share_token' => Str::random(32), 'is_public' => true]);

        $response = $this->get(route('trip.index_shared', ['token' => $trip->share_token]));

        $response->assertStatus(200);
        $response->assertSee($trip->name);
    }

    /**
     * Test guest can view a shared day via token.
     */
    public function test_guest_view_shared_day()
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create(['user_id' => $user->id, 'share_token' => Str::random(32), 'is_public' => true]);
        ItineraryDay::factory()->create(['trip_id' => $trip->id, 'date' => '2025-04-01']);

        $response = $this->get(route('day.show_shared', ['token' => $trip->share_token, 'date' => '4-1']));

        $response->assertStatus(200);
        $response->assertViewHas('day');
    }

    /**
     * Test adding a collaborator.
     */
    public function test_adding_collaborator()
    {
        $owner = User::factory()->create(['username' => 'owner']);
        $collaborator = User::factory()->create(['username' => 'collab_user', 'email' => 'collab@example.com']);
        $trip = Trip::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson(route('trip.collaborators.add', ['user' => 'owner', 'trip' => $trip->id]), [
            'email' => 'collab@example.com'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('trip_user', [
            'trip_id' => $trip->id,
            'user_id' => $collaborator->id
        ]);
        
        // Assert collaborator can now see the trip in their index
        $this->actingAs($collaborator)->get(route('home', ['user' => 'collab_user']))->assertSee($trip->name);
    }

    /**
     * Test removing a collaborator.
     */
    public function test_removing_collaborator()
    {
        $owner = User::factory()->create(['username' => 'owner']);
        $collaborator = User::factory()->create(['username' => 'collab_user', 'email' => 'collab@example.com']);
        $trip = Trip::factory()->create(['user_id' => $owner->id]);
        $trip->collaborators()->attach($collaborator->id, ['role' => 'editor']);

        $response = $this->actingAs($owner)->deleteJson(route('trip.collaborators.remove', ['user' => 'owner', 'trip' => $trip->id, 'collaborator' => $collaborator->id]), [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('trip_user', [
            'trip_id' => $trip->id,
            'user_id' => $collaborator->id
        ]);
    }
}
