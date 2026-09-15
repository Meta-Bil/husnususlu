<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('slug');
            $table->json('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('wp_id')->nullable();
            $table->timestamps();

            foreach (['tr', 'en', 'ru', 'ar'] as $locale) {
                $table->string("slug_{$locale}")->nullable()
                    ->virtualAs("json_unquote(json_extract(`slug`, '$.\"{$locale}\"'))");
                $table->index("slug_{$locale}");
            }

            $table->index('wp_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_categories');
    }
};
