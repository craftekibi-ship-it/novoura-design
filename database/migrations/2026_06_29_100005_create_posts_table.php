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
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('catalog_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('durum')->default('taslak');   // taslak | onay_bekliyor | onayli | planlandi | paylasildi
            $table->dateTime('planlanan_tarih')->nullable();
            $table->text('caption')->nullable();
            $table->json('gorsel_yazilari_json')->nullable(); // {title: "...", features: "..."}
            $table->json('foto_ids_json')->nullable();        // [asset_id, ...] (carousel için sıralı)
            $table->string('export_yolu')->nullable();
            $table->string('ig_media_id')->nullable();        // Faz 2
            $table->timestamps();

            $table->index(['brand_id', 'durum']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
