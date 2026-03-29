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
            $table->integer('bg_opacity')->default(100)->after('background_image');
            $table->integer('bg_blur')->default(0)->after('bg_opacity');
            $table->string('bg_style')->default('full')->after('bg_blur'); // 'full' or 'center'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bg_opacity', 'bg_blur', 'bg_style']);
        });
    }
};
