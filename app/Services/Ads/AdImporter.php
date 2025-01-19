<?php

namespace App\Services\Ads;

use App\Models\Account;
use App\Models\Ad;
use App\Services\Avito;
use mysql_xdevapi\Exception;

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
            if ($sleep) {
                sleep(random_int(2, 4));
            }


            $data = $this->avito->getItems(page: $page);

            $resources = $data['resources'] ?? [];

            foreach ($resources as $resource) {
                try {
                    Ad::updateOrInsert([
                        "account_id" => $account->id,
                        "external_id" => $resource['id']
                    ], [
                        "price" => $resource['price'] ?? 0,
                        "title" => $resource['title'],
                        "url" => $resource['url'],
                        'created_at' => now()
                    ]);
                } catch (\Exception $e) {

                    logger()?->error(
                        json_encode($resource, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                    );

                    throw new Exception($e->getMessage(), 0, $e);
                }
            }


            $page++;
        } while (!empty($resources));
    }
}
