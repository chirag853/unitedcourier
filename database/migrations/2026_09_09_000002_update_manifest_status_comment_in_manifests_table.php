<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Update the manifests.status column comment to reflect the new mapping:
     *   0 = open, 1 = remove, 3 = close, 4 = pickup
     */
    public function up(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->comment('0=open, 1=remove, 3=close, 4=pickup')->change();
        });
    }

    /**
     * Reverse back to the previous comment.
     */
    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->comment('0=open, 1=remove, 3=close, 4=pickup')->change();
        });
    }
};
