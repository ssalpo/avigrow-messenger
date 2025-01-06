<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\Avito;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvitoController extends Controller
{
    public function __construct(
        protected Avito $avito
    )
    {
    }

    public function getAccounts(): JsonResponse
    {
        return response()->json(
            Account::all()->map(fn($a) => [
                'id' => $a->id,
                'name' => $a->name
            ])
        );
    }

    public function getMessages(int $accountId, string $chatId): JsonResponse
    {
        $account = Account::relatedToMe()->findOrFail($accountId);
        $this->avito->setAccount($account)->markChatAsRead($chatId);

        $response = $this->avito->setAccount($account)->getChatMessages($chatId, 30, request('page', 1));

        $me = $this->avito->me();

        return response()->json([
            'has_more' => $response['meta']['has_more'],
            'messages' => collect($response['messages'])
                ->map(function ($message) use ($me) {
                    return [
                        'id' => $message['id'],
                        'is_me' => $message['author_id'] === $me->id,
                        'content_type' => $message['type'],
                        'content' => $message['content'],
                        'is_read' => $message['isRead'],
                        'created_at' => Carbon::createFromTimestamp($message['created'])->format('Y.m.d, H:i'),
                        'created_at_timestamp' => $message['created']
                    ];
                })
                ->sortBy([
                    fn(array $a, array $b) => $a['created_at_timestamp'] <=> $b['created_at_timestamp'],
                ])
                ->values()
        ]);
    }

    public function sendMessage(Request $request, int $accountId, string $chatId): JsonResponse
    {
        $request->validate([
            'message' => 'required|array',
            'message.text' => 'required'
        ]);

        $account = Account::relatedToMe()->findOrFail($accountId);

        $message = $this->avito
            ->setAccount($account)
            ->sendMessage($chatId, $request->post('message'));

        return response()->json(
            [
                'id' => $message['id'],
                'is_me' => true,
                'type' => $message['type'],
                'content' => $message['content'],
                'is_read' => false,
                'created_at' => Carbon::createFromTimestamp($message['created'], 'Asia/Dushanbe')->format('Y.m.d, H:i'),
                'created_at_timestamp' => $message['created']
            ]
        );
    }

    public function deleteMessage(int $accountId, string $chatId,  string $messageId): void
    {
        $account = Account::relatedToMe()->findOrFail($accountId);
        $this->avito->setAccount($account)->deleteMessage($chatId, $messageId);
    }
}
