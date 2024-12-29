<?php

namespace App\Services\Ads;

use App\Models\Account;
use App\Models\Ad;
use App\Services\Avito;

class AdImporter
{
    public function __construct(
        public Avito $avito
    )
    {
    }

    public function allForAccount(Account $account, bool $sleep = false): void
    {
        $page = 1;

        $this->avito->setAccount($account);

        do {
            if($sleep) {
                sleep(random_int(2, 4));
            }

            $data = $this->avito->getItems(page: $page);

            $resources = $data['resources'] ?? [];

            foreach ($resources as $resource) {
                Ad::updateOrInsert([
                    "account_id" => $account->id,
                    "external_id" => $resource['id']
                ], [
                    "price" => $resource['price'],
                    "title" => $resource['title'],
                    "url" => $resource['url'],
                    'created_at' => now()
                ]);
            }

            $page++;
        } while (!empty($resources));
    }
}
