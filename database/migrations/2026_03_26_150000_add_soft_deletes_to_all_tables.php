<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('itinerary_days', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('itinerary_events', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('checklist_items', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('trips', fn($t) => $t->dropSoftDeletes());
        Schema::table('itinerary_days', fn($t) => $t->dropSoftDeletes());
        Schema::table('itinerary_events', fn($t) => $t->dropSoftDeletes());
        Schema::table('expenses', fn($t) => $t->dropSoftDeletes());
        Schema::table('checklist_items', fn($t) => $t->dropSoftDeletes());
    }
};
