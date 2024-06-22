<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Account;
use App\Services\Avito;
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

    public function chats(Account $account): JsonResponse
    {
        $response = $this->avito->setAccount($account)->getChats(50, request('page', 1));

        return response()->json([
            'chats' => collect($response['chats'])->map(
                fn($chat) => $this->chatResponse($chat, $account)
            ),
            'has_more' => $response['meta']['has_more'],
        ]);
    }

    public function chatInfo(Account $account, string $chatId): JsonResponse
    {
        $chat = $this->avito->setAccount($account)->getChatInfoById($chatId);

        return response()->json(
            $this->chatResponse($chat, $account)
        );
    }

    public function getMessages(Account $account, string $chatId): JsonResponse
    {
        $this->avito->setAccount($account)->markChatAsRead($chatId);

        $response = $this->avito->setAccount($account)->getChatMessages($chatId, 50, request('page', 1));

        return response()->json([
            'has_more' => $response['meta']['has_more'],
            'messages' => collect($response['messages'])
                ->reverse()
                ->map(function ($message) {
                    return [
                        'id' => $message['id'],
                        'content_type' => $message['type'],
                        'content' => $message['content'],
                        'is_read' => $message['isRead'],
                        'created_at' => $message['created']
                    ];
                })
        ]);
    }

    public function sendMessage(Request $request, Account $account, string $chatId): void
    {
        $request->validate([
            'message' => 'required|array',
            'message.text' => 'required'
        ]);

        $this->avito
            ->setAccount($account)
            ->sendMessage($chatId, $request->post('message'));
    }

    public function handleWebhook(Request $request, Account $account): void
    {
        NewMessage::dispatch($account->id, [
            'unreadChatIds' => $this->avito->setAccount($account)->getUnreadChatIds(),
            'chat' => $request->post('payload', [])
        ]);
    }

    protected function chatResponse(array $chat, Account $account): array
    {
        $user = collect($chat['users'])->whereNotIn('id', [$account->external_id])->last();

        return [
            'id' => $chat['id'],
            'context_id' => $chat['context']['value']['id'],
            'context' => $chat['context']['value']['title'],
            'image' => $chat['context']['value']['images']['main']['140x105'] ?? null,
            'price' => $chat['context']['value']['price_string'] ?? '',
            'url' => $chat['context']['value']['url'],
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'avatar' => $user['public_user_profile']['avatar']['default'],
            ],
            'last_message' => [
                'id' => $chat['last_message']['id'],
                'content_type' => $chat['last_message']['type'],
                'content' => $chat['last_message']['content'],
                'created' => $chat['last_message']['created'],
                'is_read' => isset($chat['last_message']['read'])
            ]
        ];
    }
}
