<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();

            $table->json('title');
            $table->json('excerpt')->nullable();
            $table->json('body')->nullable();

            $table->string('cover_image_path')->nullable();
            $table->string('author_name')->nullable();
            $table->unsignedSmallInteger('reading_minutes')->nullable();

            // Null means draft. A future date means scheduled.
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
