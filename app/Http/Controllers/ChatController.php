<?php

namespace App\Http\Controllers;

use App\Events\MarkedAsReadChat;
use App\Services\ActiveConversationService;
use App\Services\Avito;
use App\Services\DTO\Avito\AvitoChatDto;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function __construct(
        protected Avito $avito
    )
    {
    }

    public function index(): Response
    {
        $activeAccount = request()->attributes->get('activeAccount');

        $this->avito->setAccount($activeAccount);

        return Inertia::render('Home', [
            'disableContainer' => true,
            'unreadChatIds' => fn() => $this->avito->getUnreadChatIds(),
            'currentPage' => \request('page', 1),

            'conversations' => function () use ($activeAccount) {
                $response = $this->avito->getChats(100, request('page', 1));

                return [
                    'chats' => collect($response['chats'])->map(
                        fn($chat) => Avito::chatResponse(AvitoChatDto::fromArray($chat), $activeAccount)
                    ),
                    'hasMore' => $response['meta']['has_more'],
                ];
            },
        ]);
    }

    public function messages(int $accountId, string $chatId): Response
    {
        $limit = 50;
        $account = request()->attributes->get('activeAccount');

        ActiveConversationService::sync($account, $chatId);

        $this->avito->setAccount($account);

        $chat = $this->avito->getChatInfoById($chatId);

        $this->avito->markChatAsRead($chatId);
        MarkedAsReadChat::dispatch($account->id, $chatId);

        $response = $this->avito->setAccount($account)->getChatMessages($chatId, $limit, request('page', 1));

        return Inertia::render('Messages', [
            'chat' => Avito::chatResponse($chat, $account),
            'has_more' => count($response['messages']) === $limit,
            'messages' => collect($response['messages'])
                ->map(function ($message) use ($account) {
                    return [
                        'id' => $message['id'],
                        'is_me' => $message['author_id'] === (int)$account->external_id,
                        'content_type' => $message['type'],
                        'content' => $message['content'],
                        'is_read' => isset($message['read']),
                        'created_at' => Carbon::createFromTimestamp($message['created'], 'Asia/Dushanbe')->format('Y.m.d, H:i'),
                        'created_at_timestamp' => $message['created'],
                        'quote' => $message['quote'] ?? null
                    ];
                })
                ->sortBy([
                    fn(array $a, array $b) => $a['created_at_timestamp'] <=> $b['created_at_timestamp'],
                ])
                ->values()
        ]);
    }

    public function chatInfo(int $accountId, string $chatId): JsonResponse
    {
        $account = request()->attributes->get('activeAccount');

        $chat = $this->avito->setAccount($account)->getChatInfoById($chatId);

        return response()->json(
            Avito::chatResponse($chat, $account)
        );
    }

    public function markAsRead(int $accountId, string $chatId): void
    {
        $account = request()->attributes->get('activeAccount');

        $this->avito->setAccount($account)->markChatAsRead($chatId);

        MarkedAsReadChat::dispatch($account->id, $chatId);
    }
}
