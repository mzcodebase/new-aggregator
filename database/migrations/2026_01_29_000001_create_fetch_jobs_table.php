<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fetch_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('source', 50)->index();
            $table->string('status', 20)->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('articles_fetched')->default(0);
            $table->unsignedInteger('articles_stored')->default(0);
            $table->unsignedInteger('articles_skipped')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['source', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fetch_jobs');
    }
};
