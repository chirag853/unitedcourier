<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('shipper_info', 'total_price')) {
            Schema::table('shipper_info', function (Blueprint $table) {
                $table->decimal('total_price', 10, 2)->nullable()->after('surcharge');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('shipper_info', 'total_price')) {
            Schema::table('shipper_info', function (Blueprint $table) {
                $table->dropColumn('total_price');
            });
        }
    }
};