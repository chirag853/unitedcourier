<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exporter_customers', function (Blueprint $table) {
            $table->unique(
                ['exporter_id', 'email'],
                'exporter_customers_exporter_email_unique'
            );
            $table->unique(
                ['exporter_id', 'phone_number'],
                'exporter_customers_exporter_phone_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('exporter_customers', function (Blueprint $table) {
            $table->dropUnique('exporter_customers_exporter_email_unique');
            $table->dropUnique('exporter_customers_exporter_phone_unique');
        });
    }
};
