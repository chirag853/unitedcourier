<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // csb_information.updated_at has a zero-date default ('0000-00-00 00:00:00')
        // which strict mode rejects during ALTER — allow invalid dates for this run.
        DB::statement("SET SESSION sql_mode='ALLOW_INVALID_DATES'");
        Schema::table('csb_information', function (Blueprint $table) {
            $table->string('inv_terms', 20)->nullable()->after('iec_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('csb_information', function (Blueprint $table) {
            $table->dropColumn('inv_terms');
        });
    }
};
