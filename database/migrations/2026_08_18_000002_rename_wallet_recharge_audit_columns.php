<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('wallet_transactions')) {
            return;
        }

        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('wallet_transactions', 'performed_by') && !Schema::hasColumn('wallet_transactions', 'user_id')) {
                $table->renameColumn('performed_by', 'user_id');
            }

            if (Schema::hasColumn('wallet_transactions', 'performed_by_type') && !Schema::hasColumn('wallet_transactions', 'user_type')) {
                $table->renameColumn('performed_by_type', 'user_type');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('wallet_transactions')) {
            return;
        }

        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('wallet_transactions', 'user_id') && !Schema::hasColumn('wallet_transactions', 'performed_by')) {
                $table->renameColumn('user_id', 'performed_by');
            }

            if (Schema::hasColumn('wallet_transactions', 'user_type') && !Schema::hasColumn('wallet_transactions', 'performed_by_type')) {
                $table->renameColumn('user_type', 'performed_by_type');
            }
        });
    }
};
