<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exporter_customers', function (Blueprint $table) {
            $table->integer('business_category_id')->nullable()->after('exporter_id');
            $table->foreign('business_category_id')
                ->references('id')
                ->on('business_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exporter_customers', function (Blueprint $table) {
            $table->dropForeign(['business_category_id']);
            $table->dropColumn('business_category_id');
        });
    }
};
