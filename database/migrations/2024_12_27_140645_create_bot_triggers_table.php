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
        Schema::create('bot_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_id')->constrained('bots');
            $table->jsonb('keywords');
            $table->text('response');
            $table->boolean('case_sensitive')->default(false);
            $table->integer('delay')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_triggers');
    }
};
