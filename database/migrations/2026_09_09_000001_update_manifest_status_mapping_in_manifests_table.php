<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Re-map the manifests.status values to the new serial mapping:
     *   0 = open, 1 = remove, 3 = close, 4 = pickup
     *
     * Old mapping: 0 = pending, 1 = pickup, 2 = remove
     *   - 0 (pending)  -> 4 (pickup)
     *   - 1 (pickup)   -> 0 (open)
     *   - 2 (remove)   -> 1 (remove)
     */
    public function up(): void
    {
        // Remap existing rows before changing the column definition.
        DB::table('manifests')->where('status', 0)->update(['status' => 4]);
        DB::table('manifests')->where('status', 1)->update(['status' => 0]);
        DB::table('manifests')->where('status', 2)->update(['status' => 1]);

        // Update the column comment/default to reflect the new mapping.
        Schema::table('manifests', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->comment('0=open, 1=remove, 3=close, 4=pickup')->change();
        });
    }

    /**
     * Reverse the status re-mapping back to the original values:
     *   - 4 (pickup)  -> 0 (pending)
     *   - 0 (open)    -> 1 (pickup)
     *   - 1 (remove)  -> 2 (remove)
     */
    public function down(): void
    {
        // Restore the column definition first.
        Schema::table('manifests', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->comment('0=pending, 1=pickup, 2=remove')->change();
        });

        // Reverse in an order that avoids clobbering values: 1->2 first, then 0->1, then 4->0.
        DB::table('manifests')->where('status', 1)->update(['status' => 2]);
        DB::table('manifests')->where('status', 0)->update(['status' => 1]);
        DB::table('manifests')->where('status', 4)->update(['status' => 0]);
    }
};
