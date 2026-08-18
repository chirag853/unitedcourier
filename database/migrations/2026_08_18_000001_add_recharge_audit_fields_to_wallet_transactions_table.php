<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->string('recharge_type', 50)->nullable()->after('reason');
            $table->unsignedInteger('user_id')->nullable()->after('recharge_type');
            $table->string('user_type', 20)->nullable()->after('user_id');

            $table->index(['user_type', 'user_id'], 'wallet_transactions_recharger_index');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex('wallet_transactions_recharger_index');
            $table->dropColumn(['recharge_type', 'user_id', 'user_type']);
        });
    }
};