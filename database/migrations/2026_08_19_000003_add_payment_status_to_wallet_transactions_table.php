<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guarded: payment_status may already exist when restoring from a backup
        // dump that was taken after this column was added out-of-band.
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('wallet_transactions', 'payment_status')) {
                $table->string('payment_status', 20)->nullable()->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('wallet_transactions', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};
