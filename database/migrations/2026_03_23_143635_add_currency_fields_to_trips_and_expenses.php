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
        Schema::table('trips', function (Blueprint $table) {
            $table->string('base_currency')->default('TWD')->after('flight_info');
            $table->string('target_currency')->default('JPY')->after('base_currency');
            $table->decimal('exchange_rate', 10, 4)->default(0.21)->after('target_currency');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->boolean('is_base_currency')->default(false)->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['is_base_currency']);
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['base_currency', 'target_currency', 'exchange_rate']);
        });
    }
};
