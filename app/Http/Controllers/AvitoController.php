<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ActiveConversation;
use App\Services\Avito;
use App\Services\DTO\Avito\AvitoChatDto;
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

    public function activeConversations(): JsonResponse
    {
        return response()->json(
            ActiveConversation::orderByDesc('created_at')->get()
        );
    }

    public function destroyActiveConversation(int $id): void
    {
        ActiveConversation::find($id)?->delete();
    }

    public function chats(Account $account): JsonResponse
    {
        $response = $this->avito->setAccount($account)->getChats(30, request('page', 1));

        return response()->json([
            'chats' => collect($response['chats'])->map(
                fn($chat) => Avito::chatResponse(AvitoChatDto::fromArray($chat), $account)
            ),
            'has_more' => $response['meta']['has_more'],
        ]);
    }

    public function chatInfo(Account $account, string $chatId): JsonResponse
    {
        $chat = $this->avito->setAccount($account)->getChatInfoById($chatId);

        return response()->json(
            Avito::chatResponse($chat, $account)
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
                'created_at' => Carbon::createFromTimestamp($message['created'], 'Asia/Dushanbe')->format('Y.m.d, H:i'),
                'created_at_timestamp' => $message['created']
            ]
        );
    }

    public function markAsRead(Account $account, string $chatId): void
    {
        $this->avito->setAccount($account)->markChatAsRead($chatId);
    }

    public function deleteMessage(Account $account, string $chatId,  string $messageId): void
    {
        $this->avito->setAccount($account)->deleteMessage($chatId, $messageId);
    }
}
