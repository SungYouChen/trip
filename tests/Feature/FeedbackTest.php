<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a user can view their feedback list.
     */
    public function test_user_can_view_feedback_index()
    {
        $user = User::factory()->create(['username' => 'tester']);
        Feedback::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('feedback.index', ['user' => 'tester']));

        $response->assertStatus(200);
        $response->assertViewHas('feedbacks');
    }

    /**
     * Test AJAX Feedback Creation.
     */
    public function test_ajax_feedback_creation()
    {
        $user = User::factory()->create(['username' => 'tester']);

        $response = $this->actingAs($user)->postJson(route('feedback.store', ['user' => 'tester']), [
            'content' => '這是我的建議！'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('feedbacks', ['content' => '這是我的建議！', 'user_id' => $user->id]);
    }

    /**
     * Test a user can delete their own feedback.
     */
    public function test_user_can_delete_own_feedback()
    {
        $user = User::factory()->create(['username' => 'tester']);
        $fb = Feedback::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson(route('feedback.destroy', ['user' => 'tester', 'feedback' => $fb->id]), [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertSoftDeleted('feedbacks', ['id' => $fb->id]);
    }

    /**
     * Test a user cannot delete another user's feedback.
     */
    public function test_user_cannot_delete_others_feedback()
    {
        $userA = User::factory()->create(['username' => 'UserA']);
        $userB = User::factory()->create(['username' => 'UserB']);
        $fbB = Feedback::factory()->create(['user_id' => $userB->id]);

        $response = $this->actingAs($userA)->deleteJson(route('feedback.destroy', ['user' => 'UserB', 'feedback' => $fbB->id]), [], ['X-Requested-With' => 'XMLHttpRequest']);
        
        $response->assertStatus(403);
    }
}
