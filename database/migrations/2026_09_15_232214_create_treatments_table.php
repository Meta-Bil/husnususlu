<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pain types and interventional procedures share one table; `kind` separates them.
     */
    public function up(): void
    {
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->string('kind')->default('pain_type');
            $table->json('title');
            $table->json('slug');
            $table->json('summary')->nullable();
            $table->json('blocks')->nullable();
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->boolean('noindex')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->json('locales_enabled');
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('needs_review')->default(false);
            $table->text('import_notes')->nullable();
            $table->unsignedBigInteger('wp_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            foreach (['tr', 'en', 'ru', 'ar'] as $locale) {
                $table->string("slug_{$locale}")->nullable()
                    ->virtualAs("json_unquote(json_extract(`slug`, '$.\"{$locale}\"'))");
                $table->index("slug_{$locale}");
            }

            $table->index(['kind', 'status', 'sort_order']);
            $table->index('wp_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};
