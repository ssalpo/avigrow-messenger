<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    private array $models = [
        'accounts',
        'bots',
        'fast_templates',
        'fm_tags'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {

        foreach ($this->models as $model) {
            Schema::table($model, function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->models as $model) {
            Schema::table($model, function (Blueprint $table) {
                $table->dropConstrainedForeignId('company_id');
            });
        }
    }
};
