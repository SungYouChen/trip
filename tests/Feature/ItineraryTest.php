<?php

namespace Tests\Feature;

use App\Models\ItineraryDay;
use App\Models\ItineraryEvent;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItineraryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user can view a specific itinerary day.
     */
    public function test_user_can_view_itinerary_day()
    {
        $user = User::factory()->create(['username' => 'tester']);
        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $day = ItineraryDay::factory()->create([
            'trip_id' => $trip->id,
            'date' => '2025-04-01'
        ]);

        $response = $this->actingAs($user)->get(route('day.show', ['user' => $user->username, 'trip' => $trip->id, 'date' => '4-1']));

        $response->assertStatus(200);
        $response->assertViewHas('day');
        $response->assertSee($day->title);
    }

    /**
     * Test AJAX Daily Summary/Hotel update.
     */
    public function test_ajax_itinerary_day_update()
    {
        $user = User::factory()->create(['username' => 'tester']);
        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $day = ItineraryDay::factory()->create(['trip_id' => $trip->id, 'date' => '2025-04-01']);

        $response = $this->actingAs($user)->putJson(route('day.update', ['user' => $user->username, 'trip' => $trip->id, 'date' => '4-1']), [
            'title' => '行程大改版',
            'summary' => '這是一段全新的總結。',
            'hotel_name' => '威斯汀飯店',
            'hotel_checkin' => '16:00',
            'hotel_checkout' => '12:00'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $day->refresh();
        $this->assertEquals('行程大改版', $day->title);
        $this->assertEquals('威斯汀飯店', $day->accommodation);
    }

    /**
     * Test adding an event (activity) to a day.
     */
    public function test_ajax_add_event_to_day()
    {
        $user = User::factory()->create(['username' => 'tester']);
        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $day = ItineraryDay::factory()->create(['trip_id' => $trip->id, 'date' => '2025-04-01']);

        $response = $this->actingAs($user)->postJson(route('events.store', ['user' => $user->username, 'trip' => $trip->id, 'date' => '4-1']), [
            'activity' => '東京晴空塔',
            'time' => '14:30',
            'icon' => 'building'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('itinerary_events', [
            'itinerary_day_id' => $day->id,
            'activity' => '東京晴空塔',
            'time' => '14:30'
        ]);
    }

    /**
     * Test deleting an event.
     */
    public function test_ajax_delete_event()
    {
        $user = User::factory()->create(['username' => 'tester']);
        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $day = ItineraryDay::factory()->create(['trip_id' => $trip->id, 'date' => '2025-04-01']);
        $event = ItineraryEvent::factory()->create(['itinerary_day_id' => $day->id]);

        $response = $this->actingAs($user)->deleteJson(route('events.destroy', ['user' => $user->username, 'event' => $event->id]), [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertSoftDeleted('itinerary_events', ['id' => $event->id]);
    }

    /**
     * Test Checklist Item Creation.
     */
    public function test_checklist_item_creation()
    {
        $user = User::factory()->create(['username' => 'tester']);
        $trip = Trip::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson(route('checklist.store', ['user' => $user->username, 'trip' => $trip->id]), [
            'name' => '必買：合利他命',
            'type' => 'shopping',
            'category' => '藥妝'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('checklist_items', [
            'trip_id' => $trip->id,
            'name' => '必買：合利他命'
        ]);
    }
}
