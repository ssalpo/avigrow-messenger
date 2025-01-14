<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Review;
use App\Services\Avito;
use Exception;
use Illuminate\Console\Command;
use Random\RandomException;

class ImportReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импортирует отзывы аккаунтов из Авито';

    /**
     * Execute the console command.
     * @throws RandomException
     */
    public function handle(Avito $avito)
    {
        $accounts = Account::all();

        foreach ($accounts as $account) {
            $this->importReviewsForAccount($account, $avito);
        }
    }

    /**
     * @throws RandomException
     */
    private function importReviewsForAccount(Account $account, Avito $avito): void
    {
        $page = 1;
        $limit = 50;
        $total = 0;

        do {
            $response = $avito->setAccount($account)->reviews($limit, $page);

            if (!isset($response['reviews']) || ($page === 1 && !isset($response['total']))) {
                throw new Exception("Invalid Reviews API response");
            }

            if ($page === 1) {
                $total = $response['total'];
            }

            foreach ($response['reviews'] as $review) {
                if ($this->canInsert($review)) {
                    Review::insert([
                        'account_id' => $account->id,
                        'external_id' => $review['id'],
                        'external_created_at' => $review['createdAt'],
                        'content' => $review['text'],
                        'item_id' => $review['item']['id'],
                        'item_title' => $review['item']['title'],
                        'sender' => $review['sender']['name'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            $page++;

            sleep(random_int(2, 4));

        } while (($page - 1) * $limit < $total);
    }

    private function canInsert(array $review): bool
    {
        return $review['stage'] === 'done' && $review['canAnswer'] && $review['score'] === 5;
    }
}
