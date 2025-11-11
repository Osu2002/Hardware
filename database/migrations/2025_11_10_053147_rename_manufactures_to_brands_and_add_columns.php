<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // If your table is `manufactures`, rename to `brands`
        if (Schema::hasTable('manufactures') && !Schema::hasTable('brands')) {
            Schema::rename('manufactures', 'brands');
        }

        Schema::table('brands', function (Blueprint $t) {
            // existing: id, title, status, featured, created_at, updated_at
            if (!Schema::hasColumn('brands', 'slug')) $t->string('slug', 140)->unique()->after('title');
            if (!Schema::hasColumn('brands', 'website_url')) $t->string('website_url', 255)->nullable()->after('slug');
            if (!Schema::hasColumn('brands', 'hotline_phone')) $t->string('hotline_phone', 40)->nullable()->after('website_url');
            if (!Schema::hasColumn('brands', 'support_email')) $t->string('support_email', 160)->nullable()->after('hotline_phone');
            if (!Schema::hasColumn('brands', 'country')) $t->string('country', 80)->nullable()->after('support_email');
            if (!Schema::hasColumn('brands', 'founded_year')) $t->integer('founded_year')->nullable()->after('country');
            if (!Schema::hasColumn('brands', 'short_description')) $t->text('short_description')->nullable()->after('founded_year');
            if (!Schema::hasColumn('brands', 'long_description')) $t->longText('long_description')->nullable()->after('short_description');
            if (!Schema::hasColumn('brands', 'seo_title')) $t->string('seo_title', 180)->nullable()->after('long_description');
            if (!Schema::hasColumn('brands', 'seo_description')) $t->string('seo_description', 240)->nullable()->after('seo_title');
            if (!Schema::hasColumn('brands', 'sort_order')) $t->integer('sort_order')->default(0)->after('seo_description');
            if (!Schema::hasColumn('brands', 'deleted_at')) $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $t) {
            if (Schema::hasColumn('brands', 'sort_order')) $t->dropColumn('sort_order');
            if (Schema::hasColumn('brands', 'seo_description')) $t->dropColumn('seo_description');
            if (Schema::hasColumn('brands', 'seo_title')) $t->dropColumn('seo_title');
            if (Schema::hasColumn('brands', 'long_description')) $t->dropColumn('long_description');
            if (Schema::hasColumn('brands', 'short_description')) $t->dropColumn('short_description');
            if (Schema::hasColumn('brands', 'founded_year')) $t->dropColumn('founded_year');
            if (Schema::hasColumn('brands', 'country')) $t->dropColumn('country');
            if (Schema::hasColumn('brands', 'support_email')) $t->dropColumn('support_email');
            if (Schema::hasColumn('brands', 'hotline_phone')) $t->dropColumn('hotline_phone');
            if (Schema::hasColumn('brands', 'website_url')) $t->dropColumn('website_url');
            if (Schema::hasColumn('brands', 'slug')) $t->dropUnique('brands_slug_unique');
            if (Schema::hasColumn('brands', 'slug')) $t->dropColumn('slug');
            if (Schema::hasColumn('brands', 'deleted_at')) $t->dropSoftDeletes();
        });

        // (Optional) rename back if you must revert
        if (Schema::hasTable('brands') && !Schema::hasTable('manufactures')) {
            Schema::rename('brands', 'manufactures');
        }
    }
};
