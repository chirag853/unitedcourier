<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Accept-before-deduct dispute workflow:
     * admin raises with remark (raised) -> customer accepts (accepted)
     * -> admin deducts from wallet (deducted).
     */
    public function up(): void
    {
        Schema::table('shipment_disputes', function (Blueprint $table) {
            $table->text('remark')->nullable()->after('values');
            $table->timestamp('accepted_at')->nullable()->after('applied_by');
            $table->unsignedBigInteger('accepted_by')->nullable()->after('accepted_at');
            $table->timestamp('deducted_at')->nullable()->after('accepted_by');
            $table->unsignedBigInteger('deducted_by')->nullable()->after('deducted_at');
        });
    }

    public function down(): void
    {
        Schema::table('shipment_disputes', function (Blueprint $table) {
            $table->dropColumn(['remark', 'accepted_at', 'accepted_by', 'deducted_at', 'deducted_by']);
        });
    }
};
