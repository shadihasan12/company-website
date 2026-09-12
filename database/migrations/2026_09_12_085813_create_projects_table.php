<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            // Product names are proper nouns and stay untranslated.
            $table->string('name');

            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('industry_id')->nullable()->constrained()->nullOnDelete();

            // The case-study narrative: which problem, what we did, what
            // changed. Research is consistent that this structure, with
            // numbers attached, is the strongest trust signal on the site.
            $table->json('summary')->nullable();
            $table->json('problem')->nullable();
            $table->json('solution')->nullable();
            $table->json('outcome')->nullable();

            // [{label, value, suffix, prefix}] — drives the results band and
            // the count-up animations.
            $table->json('metrics')->nullable();

            $table->string('hero_image_path')->nullable();
            // Ordered list of screenshot paths.
            $table->json('gallery')->nullable();

            $table->string('website_url')->nullable();
            $table->string('app_store_url')->nullable();
            $table->string('google_play_url')->nullable();

            // Duration only. Team size is deliberately not modelled — the
            // site does not disclose headcount anywhere.
            $table->json('duration')->nullable();
            $table->date('completed_at')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'is_featured', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
