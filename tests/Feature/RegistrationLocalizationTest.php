<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationLocalizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that duplicate registration returns Traditional Chinese validation errors in JSON format.
     */
    public function test_duplicate_registration_returns_chinese_errors()
    {
        // 1. Create an existing user
        User::factory()->create([
            'username' => 'existing_user',
            'email' => 'existing@example.com',
        ]);

        // 2. Try to register with the same username and email via AJAX
        $response = $this->postJson(route('register.post'), [
            'name' => 'Test User',
            'username' => 'existing_user',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        // 3. Assertions
        $response->assertStatus(422);
        
        // Assert json validation errors using the built-in helper (it handles unicode automatically)
        $response->assertJsonValidationErrors([
            'username' => '帳號 ID 已經被使用了。',
            'email' => '電子郵件 已經被使用了。',
        ]);
    }

    /**
     * Test successful login returns Chinese success message in JSON
     */
    public function test_login_success_returns_chinese_message()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson(route('login.post'), [
            'email' => $user->email,
            'password' => 'password123'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => '登入成功！'
                 ]);
        
        // Also check that the redirect URL contains the username
        $this->assertEquals(route('home', ['user' => $user->username]), $response->json('redirect'));
    }
}
