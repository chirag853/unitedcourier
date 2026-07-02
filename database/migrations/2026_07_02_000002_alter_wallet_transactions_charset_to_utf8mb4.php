<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Change wallet_transactions table charset to utf8mb4 so that
 * multi-byte characters like the ₹ (rupee) symbol can be stored
 * in the description / reference columns.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `wallet_transactions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `wallet_transactions` CONVERT TO CHARACTER SET latin1 COLLATE latin1_swedish_ci');
    }
};
