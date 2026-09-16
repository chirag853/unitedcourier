<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('dispute_charges', 'dispute_surcharge_charges');
    }

    public function down(): void
    {
        Schema::rename('dispute_surcharge_charges', 'dispute_charges');
    }
};
