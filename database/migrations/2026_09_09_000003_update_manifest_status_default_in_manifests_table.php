<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Set the manifests.status column default to 0 (open).
     * Mapping: 0 = open, 1 = remove, 3 = close, 4 = pickup
     */
    public function up(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->comment('0=open, 1=remove, 3=close, 4=pickup')->change();
        });
    }

    /**
     * Restore the previous default of 4 (pickup).
     */
    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->tinyInteger('status')->default(4)->comment('0=open, 1=remove, 3=close, 4=pickup')->change();
        });
    }
};
