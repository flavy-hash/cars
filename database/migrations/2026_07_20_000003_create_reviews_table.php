<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Never shown publicly — kept so staff can verify or follow up.
            $table->string('email');
            $table->unsignedTinyInteger('rating');
            $table->text('body');
            // Free text like "Bought a Toyota Harrier"; optional, and staff can
            // tidy it while moderating.
            $table->string('context')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
