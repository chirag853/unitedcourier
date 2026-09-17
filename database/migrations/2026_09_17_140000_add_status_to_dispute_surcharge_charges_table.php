<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * dispute_surcharge_charges me status column (1 = active, 0 = inactive).
     * Default 1 taaki existing saare rules active rahein.
     */
    public function up(): void
    {
        Schema::table('dispute_surcharge_charges', function (Blueprint $table) {
            if (! Schema::hasColumn('dispute_surcharge_charges', 'status')) {
                $table->boolean('status')->default(true)->after('place_of_apply');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dispute_surcharge_charges', function (Blueprint $table) {
            if (Schema::hasColumn('dispute_surcharge_charges', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
