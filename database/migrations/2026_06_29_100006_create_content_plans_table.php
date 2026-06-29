<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('ay');                          // 2026-07
            $table->json('plan_json')->nullable();
            $table->string('durum')->default('taslak');    // taslak | onayli
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_plans');
    }
};
