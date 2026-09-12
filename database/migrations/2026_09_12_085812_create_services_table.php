<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            // Matches the keys in config('site.services') so the navigation
            // and the seeded records stay in step.
            $table->string('key')->unique();
            $table->string('slug')->unique();

            $table->json('title');
            $table->json('tagline')->nullable();
            $table->json('excerpt')->nullable();
            $table->json('body')->nullable();

            // Bullet list of what the engagement includes.
            $table->json('inclusions')->nullable();
            // [{question, answer}] — also feeds FAQPage schema in T7.2.
            $table->json('faqs')->nullable();

            $table->string('icon')->default('sparkles');
            $table->json('starting_price')->nullable();
            $table->json('timeline')->nullable();

            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
