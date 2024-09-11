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
        Schema::create('code_keys', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('receipt_by')->nullable()->constrained('users');
            $table->dateTime('receipt_at')->nullable();
            $table->tinyInteger('product_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_keys');
    }
};
