<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendImageRequest;
use App\Models\Account;
use App\Services\Avito;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        public Avito $avito
    )
    {
    }

    public function sendImage(int $accountId, string $chatId, SendImageRequest $request): RedirectResponse
    {
        $file = $request->file('image');

        $this->avito->setAccount(Account::findOrFail($accountId));

        $image = $this->avito->uploadImage(
            $file?->getPathname(), $file?->getClientOriginalName()
        );

        $message = $this->avito->sendImageMessage($chatId, $image['id']);

        return redirect()->back()->with(['backData' => [
            'imageSendResponse' => [
                'id' => $message['id'],
                'is_me' => true,
                'type' => $message['type'],
                'content' => $message['content'],
                'is_read' => false,
                'created_at' => Carbon::createFromTimestamp($message['created'] / 1e9, 'Asia/Dushanbe')->format('Y.m.d, H:i'),
                'created_at_timestamp' => $message['created']
            ]
        ]]);
    }
}
