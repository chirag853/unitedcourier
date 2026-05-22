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
        // Step 1: Add all new columns
        Schema::table('blogs', function (Blueprint $table) {
            // New columns from user schema
            $table->string('blog_title', 255)->after('id');
            $table->string('url_title', 255)->after('blog_title');
            $table->unsignedBigInteger('category_id')->nullable()->after('url_title');
            $table->string('sub_heading', 255)->nullable()->after('slug');
            $table->text('sub_content')->nullable()->after('sub_heading');
            $table->string('seo_meta_title', 255)->nullable()->after('sub_content');
            $table->string('image_alt', 255)->nullable()->after('seo_meta_title');
            $table->string('social_title', 255)->nullable()->after('image_alt');
            $table->string('country_name', 100)->nullable()->after('social_title');
            $table->string('state_name', 100)->nullable()->after('country_name');
            $table->string('city_name', 100)->nullable()->after('state_name');
            $table->longText('blog_description')->nullable()->after('city_name');
            $table->text('meta_description')->nullable()->after('blog_description');
            $table->text('meta_keyword')->nullable()->after('meta_description');
            $table->string('og_title', 255)->nullable()->after('meta_keyword');
            $table->string('og_url', 255)->nullable()->after('og_title');
            $table->text('og_description')->nullable()->after('og_url');
            $table->string('og_image_url', 255)->nullable()->after('og_description');
            $table->string('twitter_card', 100)->nullable()->after('og_image_url');
            $table->string('master_image', 255)->nullable()->after('twitter_card');
            $table->string('master_image_alt_text', 255)->nullable()->after('master_image');
            $table->enum('is_trending', ['Yes', 'No'])->default('No')->after('master_image_alt_text');
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('is_trending');
            $table->text('author_description')->nullable()->after('author_name');
            $table->string('author_image', 255)->nullable()->after('author_description');
            $table->text('feed')->nullable()->after('author_image');
        });

        // Step 2: Migrate existing data from old columns to new columns
        $blogs = DB::table('blogs')->get();
        foreach ($blogs as $blog) {
            // Look up category_id from blog_categories table by slug
            $categoryId = null;
            if (!empty($blog->category)) {
                $category = DB::table('blog_categories')->where('slug', $blog->category)->first();
                $categoryId = $category ? $category->id : null;
            }

            // Map is_active boolean to status ENUM
            $status = $blog->is_active ? 'Active' : 'Inactive';

            DB::table('blogs')
                ->where('id', $blog->id)
                ->update([
                    'blog_title' => $blog->title ?? '',
                    'url_title' => $blog->slug ?? '',
                    'category_id' => $categoryId,
                    'blog_description' => $blog->content,
                    'master_image' => $blog->image,
                    'author_image' => $blog->author_avatar,
                    'status' => $status,
                ]);
        }

        // Step 3: Drop old columns that are being replaced or removed
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'image',
                'category',
                'read_time',
                'excerpt',
                'content',
                'author_avatar',
                'author_role',
                'publish_date',
                'is_active',
                'detail_sections',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore old columns
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->string('image')->nullable()->after('slug');
            $table->string('category')->after('image');
            $table->string('read_time')->nullable()->after('category');
            $table->text('excerpt')->nullable()->after('read_time');
            $table->longText('content')->nullable()->after('excerpt');
            $table->string('author_avatar')->nullable()->after('author_name');
            $table->string('author_role')->nullable()->after('author_avatar');
            $table->date('publish_date')->nullable()->after('author_role');
            $table->boolean('is_active')->default(true)->after('publish_date');
            $table->json('detail_sections')->nullable()->after('is_active');
        });

        // Restore data from new columns back to old columns
        $blogs = DB::table('blogs')->get();
        foreach ($blogs as $blog) {
            // Get category slug from category_id
            $categorySlug = '';
            if ($blog->category_id) {
                $cat = DB::table('blog_categories')->where('id', $blog->category_id)->first();
                $categorySlug = $cat ? $cat->slug : '';
            }

            DB::table('blogs')
                ->where('id', $blog->id)
                ->update([
                    'title' => $blog->blog_title ?? '',
                    'image' => $blog->master_image,
                    'category' => $categorySlug,
                    'content' => $blog->blog_description,
                    'author_avatar' => $blog->author_image,
                    'is_active' => $blog->status === 'Active' ? 1 : 0,
                ]);
        }

        // Drop new columns
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn([
                'blog_title',
                'url_title',
                'category_id',
                'sub_heading',
                'sub_content',
                'seo_meta_title',
                'image_alt',
                'social_title',
                'country_name',
                'state_name',
                'city_name',
                'blog_description',
                'meta_description',
                'meta_keyword',
                'og_title',
                'og_url',
                'og_description',
                'og_image_url',
                'twitter_card',
                'master_image',
                'master_image_alt_text',
                'is_trending',
                'status',
                'author_description',
                'author_image',
                'feed',
            ]);
        });
    }
};