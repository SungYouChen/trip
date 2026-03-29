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
        Schema::create('itinerary_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_day_id')->constrained()->onDelete('cascade');
            $table->string('time');
            $table->string('activity');
            $table->json('sub_activities')->nullable();
            $table->text('note')->nullable();
            $table->string('map_query')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_events');
    }
};
