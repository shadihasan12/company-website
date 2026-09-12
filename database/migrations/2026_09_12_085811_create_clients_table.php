<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');

            // Naming a client publicly requires their written permission.
            // When it is absent, `is_named` is false and the site shows
            // `anonymous_label` ("a leading logistics company in the Gulf")
            // instead of the real name, which stays on record internally.
            $table->boolean('is_named')->default(false);
            $table->json('anonymous_label')->nullable();

            $table->string('logo_path')->nullable();
            $table->string('website_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_featured', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
