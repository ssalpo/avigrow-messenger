<?php

namespace App\Jobs;

use App\Models\FastTemplate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class IncrementUsingFastTemplate implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $currentCompanyId,
        public int $fastTemplateId,
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        FastTemplate::currentCompany($this->currentCompanyId)
            ->whereId($this->fastTemplateId)
            ->increment('number_of_uses');
    }
}
