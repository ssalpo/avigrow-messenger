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
        Schema::create('fast_template_fm_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fast_template_id')->constrained('fast_templates')->onDelete('cascade');
            $table->foreignId('fm_tag_id')->constrained('fm_tags')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fast_template_fm_tag');
    }
};
