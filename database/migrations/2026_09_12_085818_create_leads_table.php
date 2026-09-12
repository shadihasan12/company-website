<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();

            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('budget_range')->nullable();
            $table->string('timeline')->nullable();
            $table->text('message')->nullable();

            // Which form produced this lead: contact, estimator, newsletter.
            $table->string('source')->default('contact');
            // Everything the estimator collected, kept verbatim.
            $table->json('payload')->nullable();

            $table->string('locale', 5)->nullable();
            $table->string('referrer')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->string('status')->default('new');
            $table->timestamp('contacted_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
