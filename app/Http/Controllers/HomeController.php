<?php

namespace App\Http\Controllers;

use App\CodeKeyType;
use App\Http\Requests\SendPaymentReceiptRequest;
use App\Models\Account;
use App\Models\ActiveConversation;
use App\Models\CodeKey;
use App\Models\ReviewSchedule;
use App\Services\ActiveConversationService;
use App\Services\Avito;
use App\Services\Telegram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __construct(
        protected Avito $avito
    )
    {
    }

    public function index(string $account = null)
    {
        $activeAccount = $account ? Account::findOrFail($account) : Account::first();
        $this->avito->setAccount($activeAccount);
        $response = $this->avito->setAccount($activeAccount)->getChats(30);

        $me = $this->avito->me();

        $unreadChatIds = $this->avito->getUnreadChatIds();

        return Inertia::render('Home', [
            'containerClass' => 'pa-0',
            'unreadChatIds' => $unreadChatIds,
            'currentUserId' => $me['id'],
            'conversations' => collect($response['chats'])->map(
                fn($chat) => $this->chatResponse($chat, $activeAccount)
            ),
            'hasMore' => $response['meta']['has_more'],
            'currentPage' => \request('page', 1)
        ]);
    }

    public function messages(int $accountId, string $chatId)
    {
        $account = Account::findOrFail($accountId);

        ActiveConversationService::sync($account, $chatId);

        $this->avito->setAccount($account);

        $this->avito->markChatAsRead($chatId);

        $chat = $this->avito->getChatInfoById($chatId);

        $response = $this->avito->setAccount($account)->getChatMessages($chatId, 30, request('page', 1));

        $me = $this->avito->me();

        $tabs = CodeKeyType::labels();
        $keys = CodeKey::whereNull('receipt_at')->orderByDesc('created_at')->get()->groupBy('product_type');

        return Inertia::render('Messages', [
            'tabs' => $tabs,
            'keys' => $keys,
            'activeAccount' => $account,
            'chat' => $this->chatResponse($chat, $account),
            'hasReviewSchedules' => ReviewSchedule::hasAnyForChatAndAccount($chatId, $account->id),
            'has_more' => $response['meta']['has_more'],
            'messages' => collect($response['messages'])
                ->map(function ($message) use ($me) {
                    return [
                        'id' => $message['id'],
                        'is_me' => $message['author_id'] === $me['id'],
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

    public function sendPaymentReceipt(SendPaymentReceiptRequest $request): RedirectResponse
    {
        Telegram::sendImage(
            config('services.telegram.reportGroup'),
            $request->url,
            $request->caption
        );

        return redirect()->back();
    }

    protected function chatResponse(array $chat, Account $account): array
    {
        $user = collect($chat['users'])->whereNotIn('id', [$account->external_id])->last();

        return [
            'id' => $chat['id'],
            'context_id' => $chat['context']['value']['id'],
            'context' => $chat['context']['value']['title'],
            'test' => $chat,
            'image' => $chat['context']['value']['images']['main']['140x105'] ?? $user['public_user_profile']['avatar']['default'] ?? null,
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
