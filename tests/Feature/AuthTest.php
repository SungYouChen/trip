<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the root route redirects to home when authenticated.
     */
    public function test_root_redirects_to_home_when_authenticated()
    {
        $user = User::factory()->create(['username' => 'tester']);
        $response = $this->actingAs($user)->get('/');
        $response->assertRedirect(route('home', ['user' => $user->username]));
    }

    /**
     * Test the root route shows welcome/login when guest.
     */
    public function test_root_shows_welcome_for_guests()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('welcome_or_login');
    }

    /**
     * Test Story page is accessible.
     */
    public function test_story_page_accessible()
    {
        $response = $this->get(route('story'));
        $response->assertStatus(200);
    }

    /**
     * Test AJAX Login Success.
     */
    public function test_ajax_login_success()
    {
        $user = User::factory()->create([
            'username' => 'tester',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson(route('login.post'), [
            'email' => $user->email,
            'password' => 'password123'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => '登入成功！'
                 ])
                 ->assertJsonStructure(['message', 'redirect']);
        
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test AJAX Login Failure returns Chinese error.
     */
    public function test_ajax_login_failure()
    {
        $response = $this->postJson(route('login.post'), [
            'email' => 'wrong@example.com',
            'password' => 'wrongpass'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(422)
                 ->assertJson([
                     'message' => '信箱或密碼錯誤。'
                 ]);
    }

    /**
     * Test AJAX Registration Success.
     */
    public function test_ajax_registration_success()
    {
        $response = $this->postJson(route('register.post'), [
            'name' => 'Elk Tester',
            'username' => 'elk_tester',
            'email' => 'tester@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => '註冊成功！'
                 ]);
        
        $this->assertDatabaseHas('users', ['username' => 'elk_tester']);
    }

    /**
     * Test Logout.
     */
    public function test_logout_redirects_to_root()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('logout'));
        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
