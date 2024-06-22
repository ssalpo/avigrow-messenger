<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
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

    public function chats(Account $account): JsonResponse
    {
        $response = $this->avito->setAccount($account)->getChats(30, request('page', 1));

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

        $response = $this->avito->setAccount($account)->getChatMessages($chatId, 30, request('page', 1));

        $me = $this->avito->me();

        return response()->json([
            'has_more' => $response['meta']['has_more'],
            'messages' => collect($response['messages'])
                ->map(function ($message) use ($me) {
                    return [
                        'id' => $message['id'],
                        'is_me' => $message['author_id'] === $me['id'],
                        'content_type' => $message['type'],
                        'content' => $message['content'],
                        'is_read' => $message['isRead'],
                        'created_at' => Carbon::createFromTimestamp($message['created'])->format('Y.m.d, H:i'),
                        'created_at_timestamp' => $message['created']
                    ];
                })
                ->sortBy([
                    fn (array $a, array $b) => $a['created_at_timestamp'] <=> $b['created_at_timestamp'],
                ])
                ->values()
        ]);
    }

    public function sendMessage(Request $request, Account $account, string $chatId): JsonResponse
    {
        $request->validate([
            'message' => 'required|array',
            'message.text' => 'required'
        ]);

        $message = $this->avito
            ->setAccount($account)
            ->sendMessage($chatId, $request->post('message'));

        return response()->json(
            [
                'id' => $message['id'],
                'is_me' => true,
                'content_type' => $message['type'],
                'content' => $message['content'],
                'is_read' => false,
                'created_at' => Carbon::createFromTimestamp($message['created'])->format('Y.m.d, H:i'),
                'created_at_timestamp' => $message['created']
            ]
        );
    }

    public function handleWebhook(Request $request, Account $account): void
    {
        $payload = (array) $request->post('payload', []);

        $me = $this->avito->setAccount($account)->me();

        $payload['value']['created_at'] = Carbon::createFromTimestamp($payload['value']['created'])->format('Y.m.d, H:i');
        $payload['is_me'] = $payload['value']['author_id'] === $me['id'];

        NewMessage::dispatch($account->id, [
            'unreadChatIds' => $this->avito->setAccount($account)->getUnreadChatIds(),
            'chat' => $payload
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

    public function markAsRead(Account $account, string $chatId): void
    {
        $this->avito->setAccount($account)->markChatAsRead($chatId);
    }
}
