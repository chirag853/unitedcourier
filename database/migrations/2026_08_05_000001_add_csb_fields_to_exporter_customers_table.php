<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exporter_customers', function (Blueprint $table) {
            $table->string('csb_type', 10)->default('csb_iv')->after('kyc_number');
            $table->boolean('is_lut')->default(false)->after('csb_type');
            $table->string('ad_code', 14)->nullable()->after('is_lut');
            $table->string('ad_code_document')->nullable()->after('ad_code');
            $table->string('iec_number', 10)->nullable()->after('ad_code_document');
            $table->string('iec_document')->nullable()->after('iec_number');
            $table->string('bank_account_number', 18)->nullable()->after('iec_document');
            $table->string('bank_type', 20)->nullable()->after('bank_account_number');
            $table->string('lut_bond_year', 7)->nullable()->after('bank_type');
            $table->date('lut_expiry_date')->nullable()->after('lut_bond_year');
            $table->string('lut_document')->nullable()->after('lut_expiry_date');
            $table->text('billing_address')->nullable()->after('lut_document');
            $table->string('billing_contact', 10)->nullable()->after('billing_address');
            $table->string('billing_email')->nullable()->after('billing_contact');
            $table->string('merchant_agreement')->nullable()->after('billing_email');
            $table->boolean('terms_accepted')->default(false)->after('merchant_agreement');
            $table->timestamp('merchant_agreement_accepted_at')->nullable()->after('terms_accepted');
        });
    }

    public function down(): void
    {
        Schema::table('exporter_customers', function (Blueprint $table) {
            $table->dropColumn([
                'csb_type',
                'is_lut',
                'ad_code',
                'ad_code_document',
                'iec_number',
                'iec_document',
                'bank_account_number',
                'bank_type',
                'lut_bond_year',
                'lut_expiry_date',
                'lut_document',
                'billing_address',
                'billing_contact',
                'billing_email',
                'merchant_agreement',
                'terms_accepted',
                'merchant_agreement_accepted_at',
            ]);
        });
    }
};
