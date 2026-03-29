<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Trip;
use Illuminate\Support\Facades\Hash;

class UserTripSeeder extends Seeder
{
    public function run(): void
    {
        // Create a default user if none exists
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('20190913'),
            ]
        );

        // Assign all orphaned trips to this user
        Trip::whereNull('user_id')->update(['user_id' => $user->id]);
    }
}
