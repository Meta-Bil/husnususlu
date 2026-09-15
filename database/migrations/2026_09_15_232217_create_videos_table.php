<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * YouTube videos shown in the gallery and on content pages.
     */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('youtube_id')->unique();
            $table->json('title');
            $table->json('description')->nullable();
            $table->string('category')->default('info');
            $table->string('channel')->nullable();
            $table->json('program')->nullable();
            $table->string('duration')->nullable();
            $table->date('published_on')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('treatment_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['category', 'sort_order']);
            $table->index(['is_visible', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
