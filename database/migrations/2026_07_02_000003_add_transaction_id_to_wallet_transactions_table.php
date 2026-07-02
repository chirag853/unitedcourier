<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a unique, human-readable `transaction_id` column to the
 * wallet_transactions table and backfills existing rows.
 *
 * Format: WT-YYYYMMDD-XXXXXX  (e.g. WT-20260702-482913)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->string('transaction_id', 30)->nullable()->after('id')->index();
        });

        // Backfill existing records with a unique transaction_id
        $existing = DB::table('wallet_transactions')
            ->orderBy('id')
            ->get(['id', 'created_at']);

        foreach ($existing as $row) {
            $datePart = $row->created_at
                ? \Illuminate\Support\Carbon::parse($row->created_at)->format('Ymd')
                : now()->format('Ymd');

            $txnId = 'WT-' . $datePart . '-' . str_pad(
                (string) random_int(0, 999999),
                6,
                '0',
                STR_PAD_LEFT
            );

            DB::table('wallet_transactions')
                ->where('id', $row->id)
                ->update(['transaction_id' => $txnId]);
        }

        // Now that every row has a value, make the column NOT NULL & unique
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->string('transaction_id', 30)->nullable(false)->change();
            $table->unique('transaction_id', 'wallet_transactions_transaction_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Drop unique index first (name may vary by DB driver)
            try {
                $table->dropUnique('wallet_transactions_transaction_id_unique');
            } catch (\Throwable $e) {
                $table->dropUnique(['transaction_id']);
            }
            $table->dropIndex(['transaction_id']);
            $table->dropColumn('transaction_id');
        });
    }
};
