<?php

namespace App\Jobs;

use App\Models\Account;
use App\Services\Ads\AdImporter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportAdForAccount implements ShouldQueue
{
    use Queueable;

    public $queue = 'import-ads';

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Account $account
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AdImporter $importer): void
    {
        $importer->allForAccount($this->account);
    }
}
