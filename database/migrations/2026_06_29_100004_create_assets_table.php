<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('dosya');                      // yüklenen foto yolu
            $table->string('shot_type')->nullable();      // dis | mutfak | yatak | banyo | yemek...
            $table->string('area')->nullable();
            $table->string('onay_durumu')->default('beklemede'); // beklemede | onayli | red
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
