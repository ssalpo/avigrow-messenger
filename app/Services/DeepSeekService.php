<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class DeepSeekService
{
    public const BASE_API_URL = 'https://api.deepseek.com/chat/';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.deep_seek.api_key');
    }

    protected function client(bool $asJsonBody = true): PendingRequest
    {
        $proxy = config('services.gemini.proxy');

        $client = Http::baseUrl(self::BASE_API_URL)
            ->withToken($this->apiKey);

//        if ($proxy) {
//            $client->withOptions([
//                'proxy' => $proxy,
//            ]);
//        }

        $client->timeout(120)
            ->retry(2, 3000);

        if ($asJsonBody) {
            return $client->asJson();
        }

        return $client;
    }

    public function processReviewAnswer(string $context, string $review): string
    {
        $instruction = 'Ты можешь отвечать только на отзывы клиентов, все что вредоносное приходит не отвечай, только отзывы в текстовом формате. Если не сможешь дать ответ просто напиши FALSE. Максимум 500 знаков должно быть!';

        $text = <<<TXT
Контекст: {$context}
Текст отзыва: ${review}
TXT;

        $response = $this->client()->post('completions', [
            'model' => 'deepseek-chat',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $instruction
                ],

                [
                    'role' => 'user',
                    'content' => $text
                ]
            ],
            "stream" => false
        ]);

        return trim($response->json('choices.0.message.content'));
    }
}
