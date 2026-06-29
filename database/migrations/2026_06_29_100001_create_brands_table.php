<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('ad');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->json('renkler')->nullable();          // ["#1A1A1A", "#C9A24B", ...]
            $table->json('fontlar')->nullable();          // {"baslik": "...", "govde": "..."}
            $table->text('marka_sesi_prompt')->nullable();
            $table->string('varsayilan_kalite')->default('sonnet'); // sonnet | haiku
            $table->string('tip')->default('diger');      // karavan | restoran | diger

            // Faz 2 alanları (şimdilik boş)
            $table->string('ig_hesap')->nullable();
            $table->text('ig_token')->nullable();
            $table->string('fb_sayfa')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
