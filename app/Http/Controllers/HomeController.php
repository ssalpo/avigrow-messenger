<?php

namespace App\Http\Controllers;

use App\Enums\CodeKeyType;
use App\Http\Requests\SendPaymentReceiptRequest;
use App\Models\Account;
use App\Models\ActiveConversation;
use App\Models\CodeKey;
use App\Models\ReviewSchedule;
use App\Services\ActiveConversationService;
use App\Services\Avito;
use App\Services\DTO\Avito\AvitoChatDto;
use App\Services\Telegram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(
        protected Avito $avito
    )
    {
    }

    public function index(string $account = null)
    {
        $activeAccount = $account ? Account::currentCompany()->findOrFail($account) : Account::currentCompany()->first();

        $this->avito->setAccount($activeAccount);

        $response = $this->avito->getChats(30);

        $unreadChatIds = $this->avito->getUnreadChatIds();

        return Inertia::render('Home', [
            'disableContainer' => true,
            'unreadChatIds' => $unreadChatIds,
            'currentUserId' => $activeAccount->external_id,
            'conversations' => collect($response['chats'])->map(
                fn($chat) => Avito::chatResponse(AvitoChatDto::fromArray($chat), $activeAccount)
            ),
            'hasMore' => $response['meta']['has_more'],
            'currentPage' => \request('page', 1)
        ]);
    }

    public function messages(int $accountId, string $chatId): Response
    {
        $account = Account::relatedToMe()->findOrFail($accountId);

        ActiveConversationService::sync($account, $chatId);

        $this->avito->setAccount($account);

        $this->avito->markChatAsRead($chatId);

        $chat = $this->avito->getChatInfoById($chatId);

        $response = $this->avito->setAccount($account)->getChatMessages($chatId, 30, request('page', 1));

        $tabs = CodeKeyType::labels();
        $keys = CodeKey::whereNull('receipt_at')->orderByDesc('created_at')->get()->groupBy('product_type');

        return Inertia::render('Messages', [
            'tabs' => $tabs,
            'keys' => $keys,
            'activeAccount' => $account,
            'chat' => Avito::chatResponse($chat, $account),
            'hasReviewSchedules' => ReviewSchedule::hasAnyForChatAndAccount($chatId, $account->id),
            'has_more' => $response['meta']['has_more'],
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

    public function sendPaymentReceipt(SendPaymentReceiptRequest $request): RedirectResponse
    {
        Telegram::sendImage(
            config('services.telegram.reportGroup'),
            $request->url,
            $request->caption
        );

        return redirect()->back();
    }
}
