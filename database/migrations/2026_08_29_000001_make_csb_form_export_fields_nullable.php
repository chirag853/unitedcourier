<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Courier / Aggregator customers complete Business KYC without the CSB-V
     * export details (AD Code, IEC Number, Bank Account Number). Those fields
     * are submitted as null, so make the columns nullable instead of NOT NULL
     * to allow the csb_forms insert to succeed.
     */
    public function up(): void
    {
        Schema::table('csb_forms', function (Blueprint $table) {
            $table->string('ad_code', 50)->nullable()->change();
            $table->string('iec_number', 50)->nullable()->change();
            $table->string('bank_account_number', 50)->nullable()->change();
            $table->enum('bank_type', ['private', 'government'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('csb_forms', function (Blueprint $table) {
            $table->string('ad_code', 50)->nullable(false)->change();
            $table->string('iec_number', 50)->nullable(false)->change();
            $table->string('bank_account_number', 50)->nullable(false)->change();
            $table->enum('bank_type', ['private', 'government'])->default('private')->nullable(false)->change();
        });
    }
};
