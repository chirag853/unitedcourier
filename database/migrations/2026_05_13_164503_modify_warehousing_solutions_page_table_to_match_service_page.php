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
        Schema::table('warehousing_solutions_page', function (Blueprint $table) {
            // Drop existing columns
            $table->dropColumn(['title', 'subtitle', 'paragraphs', 'list_items', 'image', 'icon_svg', 'badge_text', 'button_text', 'button_url', 'stat_number', 'stat_label']);
            
            // Rename section_key to section and change to match service_page structure
            $table->renameColumn('section_key', 'section');
            $table->string('section', 50)->change();
            
            // Add item_key column
            $table->string('item_key', 100)->after('section')->nullable();
            
            // Add content column (JSON)
            $table->json('content')->after('item_key')->nullable();
            
            // Add unique constraint
            $table->unique(['section', 'item_key']);
            
            // Add index
            $table->index(['section', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehousing_solutions_page', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['item_key', 'content']);
            
            // Drop unique constraint and index
            $table->dropUnique(['section', 'item_key']);
            $table->dropIndex(['section', 'is_active', 'sort_order']);
            
            // Rename section back to section_key
            $table->renameColumn('section', 'section_key');
            $table->string('section_key')->change();
            
            // Add back old columns
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('paragraphs')->nullable();
            $table->json('list_items')->nullable();
            $table->string('image')->nullable();
            $table->text('icon_svg')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('stat_number')->nullable();
            $table->string('stat_label')->nullable();
        });
    }
};
