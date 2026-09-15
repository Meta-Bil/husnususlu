<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Blog posts. `body` holds sanitized HTML per locale, `faq` an optional
     * list of question/answer pairs rendered as an accordion and as JSON-LD.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_category_id')->nullable()->constrained()->nullOnDelete();
            $table->json('title');
            $table->json('slug');
            $table->json('excerpt')->nullable();
            $table->json('body')->nullable();
            $table->json('faq')->nullable();
            $table->string('video_youtube_id')->nullable();
            $table->unsignedSmallInteger('reading_time')->nullable();
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->boolean('noindex')->default(false);
            $table->json('locales_enabled');
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
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
        Schema::dropIfExists('posts');
    }
};
