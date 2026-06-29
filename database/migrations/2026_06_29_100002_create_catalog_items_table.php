<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('tip')->default('menu');       // model | menu
            $table->string('ad');
            $table->string('kategori')->nullable();
            // spec_json: gerçek bilgi burada tutulur (uydurma yok)
            // restoran: {aciklama, malzemeler[], gramaj, vejetaryen, sef_ozel, alerjen[]}
            // karavan: {ozellikler[], olculer, donanim[]}
            $table->json('spec_json')->nullable();
            $table->decimal('fiyat', 10, 2)->nullable();
            $table->string('durum')->default('aktif');    // aktif | pasif
            $table->string('tanitim_acisi')->nullable();  // imza | yeni | mevsimlik...
            $table->timestamps();

            $table->index(['brand_id', 'kategori']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_items');
    }
};
