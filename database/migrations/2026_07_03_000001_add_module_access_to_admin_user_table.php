<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admin_user', function (Blueprint $table) {
            // JSON column to store the list of module keys the user can access.
            // Super Admin (type = 'Super Admin') bypasses this check entirely.
            if (!Schema::hasColumn('admin_user', 'module_access')) {
                $table->json('module_access')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_user', function (Blueprint $table) {
            if (Schema::hasColumn('admin_user', 'module_access')) {
                $table->dropColumn('module_access');
            }
        });
    }
};
