<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('shipper_info', 'custom_label')) {
            Schema::table('shipper_info', function (Blueprint $table) {
                $table->longText('custom_label')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('shipper_info', 'custom_label')) {
            Schema::table('shipper_info', function (Blueprint $table) {
                $table->dropColumn('custom_label');
            });
        }
    }
};
