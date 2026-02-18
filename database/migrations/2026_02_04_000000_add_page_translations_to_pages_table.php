<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pages')) {
            return;
        }

        Schema::table('pages', function (Blueprint $table) {
            if (! Schema::hasColumn('pages', 'title_translations')) {
                $table->json('title_translations')->nullable()->after('title');
            }
            if (! Schema::hasColumn('pages', 'excerpt_translations')) {
                $table->json('excerpt_translations')->nullable()->after('excerpt');
            }
            if (! Schema::hasColumn('pages', 'meta_title_translations')) {
                $table->json('meta_title_translations')->nullable()->after('meta_title');
            }
            if (! Schema::hasColumn('pages', 'meta_description_translations')) {
                $table->json('meta_description_translations')->nullable()->after('meta_description');
            }
            if (! Schema::hasColumn('pages', 'meta_keywords_translations')) {
                $table->json('meta_keywords_translations')->nullable()->after('meta_keywords');
            }
            if (! Schema::hasColumn('pages', 'content_translations')) {
                $table->json('content_translations')->nullable()->after('content');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pages')) {
            return;
        }

        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'title_translations')) {
                $table->dropColumn('title_translations');
            }
            if (Schema::hasColumn('pages', 'excerpt_translations')) {
                $table->dropColumn('excerpt_translations');
            }
            if (Schema::hasColumn('pages', 'meta_title_translations')) {
                $table->dropColumn('meta_title_translations');
            }
            if (Schema::hasColumn('pages', 'meta_description_translations')) {
                $table->dropColumn('meta_description_translations');
            }
            if (Schema::hasColumn('pages', 'meta_keywords_translations')) {
                $table->dropColumn('meta_keywords_translations');
            }
            if (Schema::hasColumn('pages', 'content_translations')) {
                $table->dropColumn('content_translations');
            }
        });
    }
};
