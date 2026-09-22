<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalize dispute statuses for the accept-before-deduct workflow:
     * - legacy 'applied' rows (old flow: already deducted from wallet) -> 'deducted'
     * - interim 'raised' rows (pending customer acceptance) -> 'applied'
     *
     * Going forward: applied (pending) -> accepted -> deducted.
     */
    public function up(): void
    {
        DB::table('shipment_disputes')
            ->where('status', 'applied')
            ->update(['status' => 'deducted', 'deducted_at' => DB::raw('updated_at')]);

        DB::table('shipment_disputes')
            ->where('status', 'raised')
            ->update(['status' => 'applied']);
    }

    public function down(): void
    {
        // Status values cannot be reliably restored; no-op.
    }
};
