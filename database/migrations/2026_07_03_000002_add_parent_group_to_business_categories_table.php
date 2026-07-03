<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_categories', function (Blueprint $table) {
            $table->string('parent_group')->nullable()->after('category_slug')->index();
        });

        // Assign default parent groups to existing categories so the
        // optgroup dropdown is populated immediately after migration.
        // Two top-level groups: "Business" and "Personal".
        $groupMap = [
            'Business' => ['B2B', 'D2C', 'eCommerce', 'Courier or Aggregator', 'Exporter', 'Marketplace'],
            'Personal' => ['Personal'],
        ];

        foreach ($groupMap as $group => $names) {
            DB::table('business_categories')
                ->whereIn('category_name', $names)
                ->update(['parent_group' => $group]);
        }

        // Ensure a "Personal" category exists for the Personal group.
        $personalExists = DB::table('business_categories')
            ->where('category_name', 'Personal')
            ->exists();

        if (!$personalExists) {
            DB::table('business_categories')->insert([
                'category_name' => 'Personal',
                'category_slug' => 'personal',
                'parent_group'  => 'Personal',
                'description'   => null,
                'status'        => 'active',
                'display_order' => 99,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_categories', function (Blueprint $table) {
            $table->dropIndex(['parent_group']);
            $table->dropColumn('parent_group');
        });
    }
};
