<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Appointment requests sent from the site's own form. Bookings made through
     * the DoktorTakvimi widget live in that platform, not here.
     */
    public function up(): void
    {
        Schema::create('appointment_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('complaint')->nullable();
            $table->string('preferred_time')->nullable();
            $table->date('preferred_date')->nullable();
            $table->text('message')->nullable();
            $table->string('locale', 5)->default('tr');
            $table->string('status')->default('new');
            $table->text('admin_notes')->nullable();
            $table->string('source_url')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('consented_at')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_requests');
    }
};
