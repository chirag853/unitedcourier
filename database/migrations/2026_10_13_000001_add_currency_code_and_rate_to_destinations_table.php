<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->string('currency_code', 10)->nullable()->after('country_code');
            $table->decimal('rate', 12, 4)->nullable()->after('currency_code');
        });

        // Store-only backfill (EUR skipped, rest stay NULL).
        $rates = [
            'US' => ['USD', 97],
            'CA' => ['CAD', 67],
            'AU' => ['AUD', 65],
            'GB' => ['GBP', 123],
            'SG' => ['SGD', 73],
            'AE' => ['AED', 26],
            'HK' => ['HKD', 12],
            'JP' => ['JPY', 0.6],
            'CH' => ['CHF', 117],
            'MY' => ['MYR', 23],
        ];
        foreach ($rates as $code => [$currency, $rate]) {
            DB::table('destinations')
                ->whereRaw('UPPER(TRIM(country_code)) = ?', [$code])
                ->update(['currency_code' => $currency, 'rate' => $rate]);
        }
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'rate']);
        });
    }
};
