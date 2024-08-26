<?php

namespace App\Http\Controllers;

use App\Services\Telegram;
use Illuminate\Http\Request;

class DigisellerWebhookController extends Controller
{
    public function order()
    {
        $message = <<<MSG
<b>Digiseller Новый заказ</b>
MSG;

        Telegram::sendMessageToExistIds($message);
    }

    public function message()
    {
        $msgTxt = \request('Message', '');

        $message = <<<MSG
<b>Digiseller Новое сообщение:</b>
$msgTxt
MSG;

        Telegram::sendMessageToExistIds($message);
    }
}
