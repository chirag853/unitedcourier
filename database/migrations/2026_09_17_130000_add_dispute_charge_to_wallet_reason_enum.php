<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * wallet_transactions.reason enum me 'dispute_charge' add karo taaki
     * Apply Dispute Charge par wallet deduction ki entry save ho sake.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `wallet_transactions` MODIFY `reason` ENUM('recharge','refund','shipment_charge','adjustment','dispute_charge') NOT NULL DEFAULT 'recharge'");
    }

    public function down(): void
    {
        // Rollback se pehle dispute_charge rows ko 'adjustment' par map karo
        // taaki enum shrink par data truncate na ho.
        DB::table('wallet_transactions')->where('reason', 'dispute_charge')->update(['reason' => 'adjustment']);
        DB::statement("ALTER TABLE `wallet_transactions` MODIFY `reason` ENUM('recharge','refund','shipment_charge','adjustment') NOT NULL DEFAULT 'recharge'");
    }
};
