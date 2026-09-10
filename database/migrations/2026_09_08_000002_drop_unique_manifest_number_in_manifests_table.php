<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow multiple shippers to share the same manifest number
     * (used by bulk manifest, where all selected shipments get one number).
     */
    public function up(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->dropUnique(['manifest_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->unique('manifest_number');
        });
    }
};
