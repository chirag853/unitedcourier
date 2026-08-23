<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipment_tracking', function (Blueprint $table): void {
            $table->string('tracking_provider', 50)->nullable()->after('status');
            $table->string('tracking_status', 100)->nullable()->after('tracking_provider');
            $table->json('tracking_response')->nullable()->after('tracking_status');
            $table->text('tracking_error')->nullable()->after('tracking_response');
            $table->timestamp('tracking_synced_at')->nullable()->after('tracking_error');
        });
    }

    public function down(): void
    {
        Schema::table('shipment_tracking', function (Blueprint $table): void {
            $table->dropColumn([
                'tracking_provider',
                'tracking_status',
                'tracking_response',
                'tracking_error',
                'tracking_synced_at',
            ]);
        });
    }
};
