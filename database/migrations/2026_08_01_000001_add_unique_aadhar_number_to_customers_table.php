<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const UNIQUE_INDEX = 'customers_aadhar_number_unique';

    /**
     * Enforce one Aadhaar number per customer at the database level.
     */
    public function up(): void
    {
        if (!Schema::hasTable('customers') || !Schema::hasColumn('customers', 'aadhar_number')) {
            return;
        }

        // Treat legacy blank values as missing values so multiple customers may remain unverified.
        DB::table('customers')
            ->whereNotNull('aadhar_number')
            ->whereRaw("TRIM(aadhar_number) = ''")
            ->update(['aadhar_number' => null]);

        if ($this->hasUniqueAadhaarIndex()) {
            return;
        }

        $duplicate = DB::table('customers')
            ->select('aadhar_number', DB::raw('COUNT(*) as total'))
            ->whereNotNull('aadhar_number')
            ->groupBy('aadhar_number')
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicate) {
            throw new \RuntimeException(
                'Cannot add the unique Aadhaar constraint because duplicate customer Aadhaar values already exist. Resolve the duplicate records and rerun the migration.'
            );
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->unique('aadhar_number', self::UNIQUE_INDEX);
        });
    }

    /**
     * Remove only the index created by this migration.
     */
    public function down(): void
    {
        if (!Schema::hasTable('customers') || !$this->hasIndexNamed(self::UNIQUE_INDEX)) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(self::UNIQUE_INDEX);
        });
    }

    private function hasUniqueAadhaarIndex(): bool
    {
        foreach (Schema::getIndexes('customers') as $index) {
            if (($index['unique'] ?? false) && ($index['columns'] ?? []) === ['aadhar_number']) {
                return true;
            }
        }

        return false;
    }

    private function hasIndexNamed(string $name): bool
    {
        foreach (Schema::getIndexes('customers') as $index) {
            if (($index['name'] ?? null) === $name) {
                return true;
            }
        }

        return false;
    }
};
