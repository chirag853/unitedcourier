<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('ups_rate', 'united_customer_rate');
    }

    public function down(): void
    {
        Schema::rename('united_customer_rate', 'ups_rate');
    }
};