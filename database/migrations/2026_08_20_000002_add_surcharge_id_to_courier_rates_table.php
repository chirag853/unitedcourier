<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('courier_rates', 'surcharge_id')) {
            Schema::table('courier_rates', function (Blueprint $table) {
                $table->json('surcharge_id')->nullable()->after('gst_amount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('courier_rates', 'surcharge_id')) {
            Schema::table('courier_rates', function (Blueprint $table) {
                $table->dropColumn('surcharge_id');
            });
        }
    }
};