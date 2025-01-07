<?php

namespace App\Services\Avito\Message\Handlers;

interface AvitoMessageHandler
{
    /**
     * @param array $content
     * @param array $params
     * @return void
     *
     * $params = [
     *      'accountId' => '2',
     *      'chatId' => 'u2i-1R3VAs3R1anQIg2Bedoymw',
     *      'accountUrl' => 'http://avigrow.ru',
     *      'accountName' => 'Санжар',
     *      'itemUrl' => 'http://avigrow.ru',
     *      'itemTitle' => 'Maxon Cinema 4D 2024+Redshift 3.5.24+Asset Browser',
     *      'price' => '550 руб.',
     *      'clientName' => 'Серегй Максимов',
     * ]
     */
    public function handle(array $content, array $params): void;
}
