<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class GeminiService
{
    public const BASE_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    protected function client(bool $asJsonBody = true): PendingRequest
    {
        $proxy = config('services.gemini.proxy');

        $client = Http::baseUrl(self::BASE_API_URL);

        if($proxy) {
            $client->withOptions([
                'proxy' => $proxy,
            ]);
        }

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

        $response = $this->client()->post('gemini-2.0-flash-exp:generateContent' . '?key=' . $this->apiKey, [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $text]
                    ]
                ]
            ],
            "systemInstruction" => [
                "role" => "user",
                "parts" => [
                    [
                        "text" => $instruction,
                    ],
                ],
            ],
            "generationConfig" => [
                "temperature" => 1,
                "topK" => 40,
                "topP" => 0.95,
                "maxOutputTokens" => 8192,
                "responseMimeType" => "text/plain",
            ],
        ]);

        return trim($response->json('candidates.0.content.parts.0.text'));
    }
}
