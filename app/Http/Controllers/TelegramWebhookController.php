<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\Avito;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function __construct(
        protected Avito $avito
    )
    {
    }

    /**
     * Чтобы установить вебхук или удалить воспользуйтесь примером
     * https://api.telegram.org/bot{TokenBot}/setWebhook?url=https://{url}/api/t-webhook
     *
     * https://api.telegram.org/bot{TokenBot}/deleteWebhook?url=https://{url}/api/t-webhook
     */
    public function __invoke()
    {
//        logger()->info(request()->all());

        try {
            $mKey = request()->has('message') ? 'message' : 'edited_message';

            $message = request()->input($mKey . '.text');

            if (!$this->canSendMessage($mKey)) {
                return;
            }

            $accountInfo = $this->accountInfo(\request()->input($mKey . '.reply_to_message.text'));

            $account = Account::findOrFail($accountInfo['accountId']);

            $this->avito
                ->setAccount($account)
                ->sendMessage($accountInfo['chatId'], ['text' => $message]);
        } catch (\Exception $exception) {
            logger()?->error($exception->getMessage());
        }
    }

    private function canSendMessage(string $mKey): bool
    {
        $isReplyToMessage = \request()->has($mKey . '.reply_to_message');
        $from = \request()->input($mKey . '.from.id');

        return (
            $isReplyToMessage &&
            in_array($from, config('services.telegram.ids'))
        );
    }

    private function accountInfo(string $text): ?array
    {
        if (preg_match('/\[(\d+), (.+?)\]/', $text, $matches)) {
            return [
                'accountId' => $matches[1],
                'chatId' => $matches[2],
            ];
        }

        return null;
    }
}
