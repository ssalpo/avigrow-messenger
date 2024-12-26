<?php

namespace App\Console\Commands;

use App\Models\FmTag;
use Illuminate\Console\Command;

class ClearUnusedFmTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-unused-fm-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаляет неиспользуемые теги';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        FmTag::query()->whereDoesntHave('fastTemplates')->delete();
    }
}
