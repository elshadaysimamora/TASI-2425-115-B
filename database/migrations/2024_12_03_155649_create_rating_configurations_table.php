<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rating_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Default Rating');
            $table->integer('min_scale')->default(1)->comment('Nilai minimum rating');
            $table->integer('max_scale')->default(5)->comment('Nilai maksimum rating');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rating_configurations');
    }
};
