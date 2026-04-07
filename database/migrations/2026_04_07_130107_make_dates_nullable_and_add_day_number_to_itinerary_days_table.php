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
        Schema::table('itinerary_days', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
            $table->unsignedInteger('day_number')->nullable()->after('date');
        });

        $trips = \Illuminate\Support\Facades\DB::table('trips')->get();
        foreach ($trips as $trip) {
            $days = \Illuminate\Support\Facades\DB::table('itinerary_days')
                ->where('trip_id', $trip->id)
                ->orderBy('date')
                ->get();
            $index = 1;
            foreach ($days as $day) {
                \Illuminate\Support\Facades\DB::table('itinerary_days')
                    ->where('id', $day->id)
                    ->update(['day_number' => $index]);
                $index++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itinerary_days', function (Blueprint $table) {
            $table->date('date')->nullable(false)->change();
            $table->dropColumn('day_number');
        });
    }
};
