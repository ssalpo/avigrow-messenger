<?php

use App\Enums\AccountConnectStatus;
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
        Schema::table('accounts', function (Blueprint $table) {
            $table->tinyInteger('connection_status')->default(AccountConnectStatus::CONNECTION_PENDING);
            $table->text('connection_errors')->nullable();
            $table->string('avito_name')->nullable();
            $table->text('avito_profile_url')->nullable();
            $table->string('webhook_handle_token')->nullable()->comment('Токен для обработки запросов от авито');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn([
                'connection_status',
                'connection_errors',
                'avito_name',
                'avito_profile_url',
                'webhook_handle_token'
            ]);
        });
    }
};
