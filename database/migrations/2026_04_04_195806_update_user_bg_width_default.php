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
            $table->integer('bg_width')->default(85)->change();
        });

        // Retroactively update users who have the old 95 default
        \DB::table('users')->where('bg_width', 95)->update(['bg_width' => 85]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('bg_width')->default(95)->change();
        });
    }
};
