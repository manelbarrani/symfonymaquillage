<?php

// src/Service/BeautyAiService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AIChatService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client, string $openrouterApiKey)
    {
        $this->client = $client;
        $this->apiKey = $openrouterApiKey;
    }

    public function getAdvice(string $question): ?string
    {
        $response = $this->client->request('POST', 'https://openrouter.ai/api/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => 'https://votre-site.fr', // Personnalisez si nécessaire
            ],
            'json' => [
                'model' => 'openai/gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are BeautyAdvices, an expert in beauty products and makeup. You help users choose what to buy based on their skin, budget, and preferences.'],
                    ['role' => 'user', 'content' => $question],
                ],
                'temperature' => 0.7
            ],
        ]);

        $data = $response->toArray(false);
        return $data['choices'][0]['message']['content'] ?? null;
    }
}
