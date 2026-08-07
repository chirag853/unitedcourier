<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const UNIQUE_INDEX = 'customers_customer_code_unique';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('customer_code', 20)
                ->nullable()
                ->after('id')
                ->comment('Unique customer code, for example UWC000001');
        });

        DB::table('customers')
            ->select('id')
            ->orderBy('id')
            ->chunkById(500, function ($customers) {
                foreach ($customers as $customer) {
                    DB::table('customers')
                        ->where('id', $customer->id)
                        ->update([
                            'customer_code' => 'UWC' . str_pad((string) $customer->id, 6, '0', STR_PAD_LEFT),
                        ]);
                }
            });

        Schema::table('customers', function (Blueprint $table) {
            $table->unique('customer_code', self::UNIQUE_INDEX);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(self::UNIQUE_INDEX);
            $table->dropColumn('customer_code');
        });
    }
};
