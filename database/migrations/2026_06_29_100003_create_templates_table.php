<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ad')->nullable();
            $table->string('tip')->default('post');       // post | story | carousel
            $table->string('shot_type')->nullable();      // dis | ic | yemek | null
            $table->string('frame_png')->nullable();      // şeffaf çerçeve PNG yolu
            $table->json('slot_json');                    // kompozisyon haritası (slotlar + text_boxes)
            $table->string('boyut')->default('1080x1350');
            $table->boolean('yazi_var_mi')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
