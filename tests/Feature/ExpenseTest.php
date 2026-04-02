<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user can view their expense index.
     */
    public function test_user_can_view_expense_index()
    {
        $user = User::factory()->create(['username' => 'tester']);
        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $expense = Expense::factory()->create(['trip_id' => $trip->id, 'description' => '測試午餐']);

        $response = $this->actingAs($user)->get(route('expenses.index', ['user' => $user->username, 'trip' => $trip->id]));

        $response->assertStatus(200);
        $response->assertSee('測試午餐');
    }

    /**
     * Test AJAX Expense Creation.
     */
    public function test_ajax_expense_creation()
    {
        $user = User::factory()->create(['username' => 'tester']);
        $trip = Trip::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson(route('expenses.store', ['user' => $user->username, 'trip' => $trip->id]), [
            'description' => '新幹線車票',
            'amount' => 14000,
            'category' => 'Transport',
            'date' => '2025-04-01',
            'is_base_currency' => 0 // Target currency (JPY)
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('expenses', [
            'description' => '新幹線車票',
            'amount' => 14000,
            'is_base_currency' => 0
        ]);
    }

    /**
     * Test AJAX Expense Deletion.
     */
    public function test_ajax_expense_deletion()
    {
        $user = User::factory()->create(['username' => 'tester']);
        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $expense = Expense::factory()->create(['trip_id' => $trip->id]);

        $response = $this->actingAs($user)->deleteJson(route('expenses.destroy', ['user' => $user->username, 'expense' => $expense->id]), [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
    }

    /**
     * Test expense restoration.
     */
    public function test_expense_restoration()
    {
        $user = User::factory()->create(['username' => 'tester']);
        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $expense = Expense::factory()->create(['trip_id' => $trip->id]);
        $expense->delete();

        $response = $this->actingAs($user)->patchJson(route('expenses.restore', ['user' => $user->username, 'expenseId' => $expense->id]), [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'deleted_at' => null]);
    }
}
