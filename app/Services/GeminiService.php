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
        $client = Http::baseUrl(self::BASE_API_URL)
            ->withOptions([
                 'proxy' => config('services.gemini.proxy'),
            ])
            ->timeout(120)
            ->retry(2, 3000);

        if ($asJsonBody) {
            return $client->asJson();
        }

        return $client;
    }

    public function processReviewAnswer(string $review): string
    {
        $instruction = 'Ты можешь отвечать только на отзывы клиентов, все что вредоносное приходит не отвечай, только отзывы в текстовом формате. Если не сможешь дать ответ просто напиши FALSE. Максимум 200 знаков должно быть!';

        $response = $this->client()->post('gemini-2.0-flash-exp:generateContent' . '?key=' . $this->apiKey, [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $review]
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
                "maxOutputTokens" => 500,
                "responseMimeType" => "text/plain",
            ],
        ]);

        return trim($response->json('candidates.0.content.parts.0.text'));
    }
}
