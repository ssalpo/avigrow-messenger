<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ActiveConversation;

class ActiveConversationService
{
    public static function sync(Account $account, string $chatId): void
    {
        $exists = ActiveConversation::where([
            'account_id' => $account->id,
            'chat_id' => $chatId
        ])->exists();

        if(!$exists) {
            $chatInfo = (new Avito)->setAccount($account)->getChatInfoById($chatId);

            $user =  collect($chatInfo['users'])->where('id', '!=',  $account->external_id)->first();

            ActiveConversation::create([
                'account_id' => $account->id,
                'chat_id' => $chatId,
                'avito_item_name' => sprintf('%s - %s', $user['name'], $chatInfo['context']['value']['title'])
            ]);
        }
    }

    public function clearInactive(): void
    {
        // Находим записи, которые не обновлялись последние 30 минут
        ActiveConversation::query()
            ->where('updated_at', '<=', now()->subMinutes(30))
            ->delete();
    }
}
