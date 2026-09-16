<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispute_surcharge_charges', function (Blueprint $table) {
            if (! Schema::hasColumn('dispute_surcharge_charges', 'gst_percentage')) {
                $table->integer('gst_percentage')->default(0)->after('values');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dispute_surcharge_charges', function (Blueprint $table) {
            if (Schema::hasColumn('dispute_surcharge_charges', 'gst_percentage')) {
                $table->dropColumn('gst_percentage');
            }
        });
    }
};
