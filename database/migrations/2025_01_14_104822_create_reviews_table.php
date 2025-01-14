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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->unsignedBigInteger('external_id');
            $table->unsignedInteger('external_created_at');
            $table->text('content');
            $table->unsignedBigInteger('item_id');
            $table->string('item_title');
            $table->string('sender');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
