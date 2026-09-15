<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Maps WordPress rows to the records they were imported into so the import
     * can be re-run without creating duplicates.
     */
    public function up(): void
    {
        Schema::create('wp_import_maps', function (Blueprint $table) {
            $table->id();
            $table->string('wp_type', 40);
            $table->unsignedBigInteger('wp_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->string('locale', 5)->nullable();
            $table->timestamps();

            $table->unique(['wp_type', 'wp_id']);
            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wp_import_maps');
    }
};
