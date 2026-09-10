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
        Schema::table('bill_to', function (Blueprint $table) {
            if (! Schema::hasColumn('bill_to', 'biller_name')) {
                $table->string('biller_name', 255)->nullable()->after('customer_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_to', function (Blueprint $table) {
            if (Schema::hasColumn('bill_to', 'biller_name')) {
                $table->dropColumn('biller_name');
            }
        });
    }
};
