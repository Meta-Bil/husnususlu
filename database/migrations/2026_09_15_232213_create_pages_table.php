<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Editorial pages. Every translatable field is a JSON map of locale => value.
     */
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->string('template')->default('default');
            $table->json('title');
            $table->json('slug');
            $table->json('excerpt')->nullable();
            $table->json('blocks')->nullable();
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->string('schema_type')->default('WebPage');
            $table->boolean('noindex')->default(false);
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

            $table->index(['status', 'published_at']);
            $table->index('wp_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
