<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('brand');
            $table->string('model');
            $table->unsignedSmallInteger('year');
            $table->string('body_type');
            $table->string('condition')->default('Used');
            $table->string('city');
            $table->string('state');
            $table->unsignedInteger('price');
            $table->unsignedInteger('mileage');
            $table->string('transmission');
            $table->string('fuel_type');
            $table->unsignedSmallInteger('seats');
            $table->unsignedSmallInteger('horsepower');
            $table->string('exterior_color');
            $table->string('badge')->nullable();
            $table->string('image');
            $table->json('gallery')->nullable();
            $table->json('features')->nullable();
            $table->text('description');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['brand', 'body_type']);
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
