<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bot_greetings', function (Blueprint $table) {
            $table->time('schedule_from')->default('00:00:00');
            $table->time('schedule_to')->default('23:59:59');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bot_greetings', function (Blueprint $table) {
            $table->dropColumn(['schedule_from', 'schedule_to']);
        });
    }
};
