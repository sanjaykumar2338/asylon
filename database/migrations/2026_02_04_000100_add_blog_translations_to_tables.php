<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('blog_categories')) {
            Schema::table('blog_categories', function (Blueprint $table) {
                if (! Schema::hasColumn('blog_categories', 'name_translations')) {
                    $table->json('name_translations')->nullable()->after('name');
                }
            });
        }

        if (Schema::hasTable('blog_posts')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                if (! Schema::hasColumn('blog_posts', 'title_translations')) {
                    $table->json('title_translations')->nullable()->after('title');
                }
                if (! Schema::hasColumn('blog_posts', 'excerpt_translations')) {
                    $table->json('excerpt_translations')->nullable()->after('excerpt');
                }
                if (! Schema::hasColumn('blog_posts', 'content_translations')) {
                    $table->json('content_translations')->nullable()->after('content');
                }
                if (! Schema::hasColumn('blog_posts', 'featured_image_alt_translations')) {
                    $table->json('featured_image_alt_translations')->nullable()->after('featured_image_alt');
                }
                if (! Schema::hasColumn('blog_posts', 'meta_title_translations')) {
                    $table->json('meta_title_translations')->nullable()->after('meta_title');
                }
                if (! Schema::hasColumn('blog_posts', 'meta_description_translations')) {
                    $table->json('meta_description_translations')->nullable()->after('meta_description');
                }
                if (! Schema::hasColumn('blog_posts', 'meta_keywords_translations')) {
                    $table->json('meta_keywords_translations')->nullable()->after('meta_keywords');
                }
                if (! Schema::hasColumn('blog_posts', 'author_name_translations')) {
                    $table->json('author_name_translations')->nullable()->after('author_name');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('blog_posts')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                if (Schema::hasColumn('blog_posts', 'author_name_translations')) {
                    $table->dropColumn('author_name_translations');
                }
                if (Schema::hasColumn('blog_posts', 'meta_keywords_translations')) {
                    $table->dropColumn('meta_keywords_translations');
                }
                if (Schema::hasColumn('blog_posts', 'meta_description_translations')) {
                    $table->dropColumn('meta_description_translations');
                }
                if (Schema::hasColumn('blog_posts', 'meta_title_translations')) {
                    $table->dropColumn('meta_title_translations');
                }
                if (Schema::hasColumn('blog_posts', 'featured_image_alt_translations')) {
                    $table->dropColumn('featured_image_alt_translations');
                }
                if (Schema::hasColumn('blog_posts', 'content_translations')) {
                    $table->dropColumn('content_translations');
                }
                if (Schema::hasColumn('blog_posts', 'excerpt_translations')) {
                    $table->dropColumn('excerpt_translations');
                }
                if (Schema::hasColumn('blog_posts', 'title_translations')) {
                    $table->dropColumn('title_translations');
                }
            });
        }

        if (Schema::hasTable('blog_categories')) {
            Schema::table('blog_categories', function (Blueprint $table) {
                if (Schema::hasColumn('blog_categories', 'name_translations')) {
                    $table->dropColumn('name_translations');
                }
            });
        }
    }
};
