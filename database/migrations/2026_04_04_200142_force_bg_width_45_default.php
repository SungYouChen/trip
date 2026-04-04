<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('bg_width')->default(45)->change();
        });

        // Force all users who have the common defaults (95/85) to 45
        \DB::table('users')->whereIn('bg_width', [85, 95])->update(['bg_width' => 45]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('bg_width')->default(85)->change();
        });
    }
};
